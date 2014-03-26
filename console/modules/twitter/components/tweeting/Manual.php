<?php
namespace console\modules\twitter\components\tweeting;

use common\helpers\Url;
use Yii;
use yii\base\Exception;
use console\components\Logger;
use common\api\twitter\Accounts;
use common\api\twitter\Apps;
use console\modules\twitter\components\Tweeting;
use common\api\finance\Operation;
use console\modules\twitter\components\Errors;
use yii\db\Query;

class Manual implements TweetingInterface
{
    use TweetingTrait;

    protected $_account;
    protected $_str_id;
    protected $_url;

    /**
     * Устанавливаем валидаторы, и инициализируем заказ
     *
     * @param $task
     */
    public function process($task)
    {
        $this->setValidators([
            'tweet-time-out',
            'domen-time-out'
        ]);

        $this->init($task);
    }

    /**
     * Выполняем размещение твиттера и денежные операций, после успешной валидации
     */
    protected function execute()
    {
        $command = Yii::$app->db->createCommand();

        if($this->postTweet() === true) {
            try {
                $t = Yii::$app->db->beginTransaction();

                /** Начисляем деньги на баланс пользователя */
                Operation::put($this->getAmountToBloger(), $this->getAccount('owner_id'), $this->getPayType(), 'tweetsCheck', $this->getStrId(), $this->getAccount('screen_name'));

                /** Добавляем в список ссылок для проверки */
                $command->insert('{{%twitter_tweets}}', [
                    'order_hash'     => $this->get('order_hash'),
                    'order_type'     => 'manual',
                    'adv_id'         => $this->getOwner(),
                    'bloger_id'      => $this->getAccount('owner_id'),
                    'tweet_hash'     => md5($this->getParams('tweet')),
                    'tweet'          => $this->getParams('tweet'),
                    'tweet_id'       => $this->getStrId(),
                    'tw_account'     => $this->getAccount('id'),
                    'date'           => date('Y-m-d H:i:s'),
                    'tweet_cost'     => $this->getAmountToBloger(),
                    'return_amount'  => $this->getAmountToBloger(),
                    'payment_method' => $this->get('payment_type'),
                ])->execute();

                $command->update('{{%twitter_ordersPerform}}', ['posted_date' => date('Y-m-d H:i:s'), 'status' => '3', 'tweet_id' => $this->getStrId()], ['id' => $this->get('sbuorder_id')])->execute();
                $command->delete('{{%twitter_tweeting}}', ['id' => $this->get('id')])->execute();

                $this->updateOrder($this->get('order_hash'), $this->get('order_id'));

                $t->commit();
            } catch(Exception $e) {
                Logger::error($e, [], 'daemons/tweeting/error', 'errorOrderProcess');
                $t->rollBack();
            }
        }
    }

    /**
     * Получаем ID размещеного твита
     *
     * @return mixed
     */
    public function getStrId()
    {
        return $this->_str_id;
    }

    /**
     * Размещаем твит
     *
     * @return bool
     */
    protected function postTweet()
    {
        echo "Run manual post - tweet: " . $this->getTweet() . "  App: " . Apps::get($this->getAccount('app'), 'id') . " Account: id " . $this->getAccount('id') . "-" . $this->getAccount('screen_name') . PHP_EOL;

        if($this->getAccount('id') !== false) {
            $tweeting = new Tweeting();

            /** Устанавливаем ключи доступа к приложению, и к аккаунту, и отсылаем твит в твиттер */
            $tweeting->set([
                'app_key'     => Apps::get($this->getAccount('app'), '_key'),
                'app_secret'  => Apps::get($this->getAccount('app'), '_secret'),
                'user_key'    => $this->getAccount('_key'),
                'user_secret' => $this->getAccount('_secret'),
                'ip'          => Apps::get($this->getAccount('app'), 'ip'),
            ])
                ->send($this->getTweet());

            /** Успешное размещение */
            if($tweeting->getCode() == 200) {
                echo "post Tweet manual success" . PHP_EOL;
                $this->_str_id = $tweeting->getStrId();

                if($this->_str_id > 0) {
                    Yii::$app->redis->set('twitter:twitting:timeout:accounts:' . $this->getAccount('id'), $this->getAccount('id'), $this->getAccount('_timeout') * 60);
                    echo "post Tweet manual: set account timeout" . PHP_EOL;

                    if($this->get('tweet') !== null || $this->get('tweet') != '') {
                        $tweet_hash = md5($this->get('tweet'));
                        Yii::$app->redis->set('console:twitter:tweeting:exclude:tweet:' . $tweet_hash, $tweet_hash, rand(180, (10 * 60)));
                        echo "post Tweet manual: set tweet timeout" . PHP_EOL;
                    }

                    $response = true;
                } else {
                    (new Errors())->errorTweetPost($this, $tweeting);
                    $response = false;
                }
            } else {
                (new Errors())->errorTweetPost($this, $tweeting);
                $timeout = rand(3, 6);
                echo "Error Post tweet, run timeout " . $timeout . " sec." . PHP_EOL;
                sleep($timeout);
                $response = false;
            }
        } else {
            (new Errors())->accountNotFound($this);
            $response = false;
        }

        if($this->getUrl() !== null) {
            echo "post Tweet manual: set domen timeout" . PHP_EOL;
            $domen = Url::getDomen($this->getUrl());

            Yii::$app->redis->set('console:twitter:tweeting:exclude:domen:' . md5($domen), $domen, rand(180, (15 * 60)));
        }

        return $response;
    }

    /**
     * Получаем аккаунт для размещение твита, аккаунты берутся по очереди, если первый раз аккаунт не найден, начинаем список заного.
     *
     * @param $key
     * @param bool $all
     * @return array|bool
     */
    public function getAccount($key, $all = false)
    {
        if($this->_account === null) {
            $this->_account = (new Accounts())->where(['id' => $this->getParams('account')])->one();
        }

        if($all === true)
            return $this->_account;
        else
            return isset($this->_account[$key]) ? $this->_account[$key] : false;
    }

    /**
     * Обновляем статус
     */
    protected function updateOrder($hash, $id)
    {
        echo "Run update order" . PHP_EOL;

        if(!empty($hash)) {
            $count = (new Query())
                ->from('{{%twitter_ordersPerform}}')
                ->where(['and', 'order_hash=:hash', ['or', 'status=0', 'status=1']], [':hash' => $hash])
                ->count();

            echo "update order count: " . $count . PHP_EOL;

            if($count == 0) {
                echo "update order is success: id " . $id . PHP_EOL;
                Yii::$app->db->createCommand()->update('{{%twitter_orders}}', ['status' => '2', 'finish_date' => date('Y-m-d H:i:s')], ['id' => $id])->execute();
            }
        }
    }

    protected function getPayType()
    {
        return $this->getParams('pay_type') == 0 ? 'purse' : 'bonus';
    }

    protected function getUrl()
    {
        if($this->_url === null) {
            preg_match_all("#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i", strtolower($this->getTweet()), $urls);

            if(!empty($urls[0])) {
                $count = count($urls[0]);

                if($count) {
                    foreach($urls[0] as $url) {
                        $this->_url = $url;
                        break;
                    }
                }
            }
        }

        return $this->_url;
    }

    /**
     * Проверяем время последнего размещеного идентичного поста, если интервал слишком маленький, пропускаем задание
     * @return boolean
     */
    protected function validateTweetTimeOut()
    {
        echo "Run validator TweetTimeOut: tweet hash" . md5($this->get('tweet')) . PHP_EOL;

        if($timeout = Yii::$app->redis->get('console:twitter:tweeting:exclude:tweet:' . md5($this->get('tweet'))))
            return false;
        else
            return true;
    }

    /**
     * Проверяем время последнего размещеного идентичного поста, если интервал слишком маленький, пропускаем задание
     * @return boolean
     */
    protected function validateDomenTimeOut()
    {
        $domen = Url::getDomen($this->getUrl());

        echo "Run validator DomenTimeOut: domen " . $domen . PHP_EOL;

        if($timeout = Yii::$app->redis->get('console:twitter:tweeting:exclude:domen:' . md5($domen)))
            return false;
        else
            return true;
    }

    public function flush()
    {
        $this->_account = null;
        $this->_str_id = null;
        $this->_url = null;
    }
}