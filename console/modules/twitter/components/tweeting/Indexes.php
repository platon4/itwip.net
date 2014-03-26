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

class Indexes implements TweetingInterface
{
    use TweetingTrait;

    protected $_account;
    protected $_str_id;

    /**
     * Список цен, и соответствующее время индексаци
     *
     * @var array
     */
    public $times = [
        5  => 12,
        10 => 9,
        20 => 6,
        40 => 3,
        65 => 1,
    ];

    /**
     * Устанавливаем валидаторы, и инициализируем заказ
     *
     * @param $task
     */
    public function process($task)
    {
        $this->setValidators([
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
                Operation::put($this->getAmountToBloger(), $this->getAccount('owner_id'), 'purse', 'indexesCheck', $this->get('sbuorder_id'), $this->getAccount('screen_name'));

                /** Добавляем в список ссылок для проверки */
                $command->insert('{{%twitter_urlCheck}}', [
                    'date_check' => date('Y-m-d H:i:s', time() + ($this->times[$this->getTime()] * 60 * 60)),
                    '_params'    => json_encode([
                        'order_id'      => $this->get('order_id'),
                        'pid'           => $this->get('sbuorder_id'),
                        'order_hash'    => $this->get('order_hash'),
                        'bloger_id'     => $this->getAccount('owner_id'),
                        'url'           => $this->getUrl(),
                        'adv_id'        => $this->getOwner(),
                        'amount'        => $this->getAmountToBloger(),
                        'amount_return' => $this->getAmountToAdv(),
                        'account_id'    => $this->getAccount('id'),
                        'tw_str_id'     => $this->getStrId()
                    ])
                ])->execute();

                $command->update('{{%twitter_ordersPerform}}', ['posted_date' => date('Y-m-d H:i:s'), 'status' => '1'], ['id' => $this->get('sbuorder_id')])->execute();
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
     * Получаем ссылку для размещение
     *
     * @return null
     */
    public function getUrl()
    {
        return $this->getParams('url');
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

    public function getTime()
    {
        return $this->getParams('time');
    }

    /**
     * Размещаем твит
     *
     * @return bool
     */
    protected function postTweet()
    {
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
                $this->_str_id = $tweeting->getStrId();

                if($this->_str_id > 0) {
                    Yii::$app->redis->set('twitter:twitting:timeout:accounts:' . $this->getAccount('id'), $this->getAccount('id'), $this->getAccount('_timeout') * 60);

                    $domen = Url::getDomen($this->getUrl());
                    Yii::$app->redis->set('console:twitter:tweeting:exclude:domen:' . md5($domen), $domen, rand(180, (15 * 60)));

                    return true;
                } else {
                    (new Errors())->errorTweetPost($this, $tweeting);
                    return false;
                }
            } else {
                (new Errors())->errorTweetPost($this, $tweeting);
                return false;
            }
        } else {
            (new Errors())->accountNotFound($this);
        }
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
        if(!empty($hash)) {
            $count = (new Query())
                ->from('{{%twitter_ordersPerform}}')
                ->where(['and', 'order_hash=:hash', ['or', 'status=0']], [':hash' => $hash])
                ->count();

            if($count == 0)
                Yii::$app->db->createCommand()->update('{{%twitter_orders}}', ['status' => '2', 'finish_date' => date('Y-m-d H:i:s')], ['id' => $id])->execute();
        }
    }

    /**
     * Проверяем время последнего размещеного идентичного поста, если интервал слишком маленький, пропускаем задание
     * @return boolean
     */
    protected function validateDomenTimeOut()
    {
        $domen = Url::getDomen($this->getUrl());

        echo "Run validator DomenTimeOut: domen " . $domen . PHP_EOL;

        if($timeout = Yii::$app->redis->get('console:twitter:tweeting:exclude:domen:' . md5($domen))) {
            echo "validator DomenTimeOut: false" . PHP_EOL;
            return false;
        } else {
            echo "validator DomenTimeOut: true" . PHP_EOL;
            return true;
        }
    }

    public function flush()
    {
        $this->_account = null;
        $this->_str_id = null;
    }
}