<?php

namespace console\modules\twitter\components\tweeting;

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
            'account-time-out',
            'tweet-time-out'
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
                    'date_check' => date('Y-m-d H:i:s', time() + ($this->times[$this->getTime()])),
                    '_params'    => json_encode([
                        'order_id'      => $this->get('order_id'),
                        'pid'           => $this->get('sbuorder_id'),
                        'bloger_id'     => $this->getAccount('owner_id'),
                        'url'           => $this->getUrl(),
                        'adv_id'        => $this->getOwner(),
                        'amount'        => $this->getAmountToBloger(),
                        'amount_return' => $this->getAmountToAdv(),
                        'tw_str_id'     => $this->getStrId()
                    ])
                ])->execute();

                $command->update('{{%twitter_ordersPerform}}', ['posted_date' => date('Y-m-d H:i:s')])->execute();
                $command->delete('{{%twitter_tweeting}}', ['id' => $this->get('id')])->execute();

                $t->commit();
            } catch(Exception $e) {
                Logger::error($e, [], 'daemons/tweeting/error', 'errorOrderProcess');
                $t->rollBack();
            }
        }

        $command->insert('{{%twitter_tweetingAccountsLogs}}', ['account_id' => $this->getAccount('id'), 'logType' => 'indexes'])->execute();
    }

    /**
     * Получаем ссылку для размещение
     *
     * @return null
     */
    protected function getUrl()
    {
        return $this->getParams('url');
    }

    /**
     * Получаем ID размещеного твита
     *
     * @return mixed
     */
    protected function getStrId()
    {
        return $this->_str_id;
    }

    protected function getTime()
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
            if($tweeting->getCode() === 200) {
                $this->_str_id = $tweeting->getStrId();
                return true;
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
    protected function getAccount($key, $all = false)
    {
        if($this->_account === null) {
            $this->_account = $this->getAccountQuery();

            if($this->_account === false) {
                Yii::$app->db->createCommand()->delete('{{%twitter_tweetingAccountsLogs}}', ['logType' => 'indexes'])->execute();
                $this->_account = $this->getAccountQuery();
            }
        }

        if($all === true)
            return $this->_account;
        else
            return isset($this->_account[$key]) ? $this->_account[$key] : false;
    }

    /**
     * Запрос для получение аккаунта
     *
     * @return array|bool
     */
    protected function getAccountQuery()
    {
        $account = (new Accounts())->where(['and', 'a._status=1', 's.in_indexses=1', ['not exists', (new Query())->select('id')->from('{{%twitter_tweetingAccountsLogs}}')->where(['and', 'logType=\'indexes\'', 'account_id=a.id'])]])->one();

        return $account;
    }

    /**
     * Проверяем время последнего поста в аккаунт, если интервал меньше чем указана в настройках аккаунта, пропускаем задание
     * @return boolean
     */
    protected function validateAccountTimeOut()
    {
        return true;
    }

    /**
     * Проверяем время последнего размещеного идентичного поста, если интервал слишком маленький, пропускаем задание
     * @return boolean
     */
    protected function validateTweetTimeOut()
    {
        return true;
    }
}