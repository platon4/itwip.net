<?php

namespace console\modules\twitter\components\tweeting;

use common\api\finance\Operation;
use Yii;
use yii\base\Exception;
use common\api\twitter\Accounts;
use common\api\twitter\Apps;
use console\modules\twitter\components\Tweeting;
use yii\db\Query;

class Indexes implements TweetingInterface
{
    use TweetingTrait;

    protected $_account;
    protected $_str_id;

    public $times = [
        5  => 12,
        10 => 9,
        20 => 6,
        40 => 3,
        65 => 1,
    ];

    public function process($task)
    {
        $this->setValidators([
            'account-time-out',
            'tweet-time-out'
        ]);

        $this->init($task);
    }

    protected function execute()
    {
        $command = Yii::$app->db->createCommand();

        if($this->postTweet() === true) {
            try {
                $t = Yii::app()->db->beginTransaction();

                Operation::put($this->getAmountToBloger(), $this->getAccount('owner_id'), 'purse', 'indexesCheck', $this->get('sbuorder_id'), $this->getAccount('screen_name'));
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
                        'tw_str_id'     => $this->getTweetID()
                    ])
                ])->execute();

                $command->update('{{%twitter_ordersPerform}}', ['posted_date' => date('Y-m-d H:i:s')])->execute();
                $command->delete('{{%twitter_tweeting}}', ['id' => $this->get('id')])->execute();

                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }
        }

        $command->insert('{{%twitter_tweetingAccountsLogs}}', ['account_id' => $this->accountGet('id'), 'logType' => 'indexes'])->execute();
    }

    protected function getUrl()
    {
        return $this->getParams('url');
    }

    protected function getTweetID()
    {
        return $this->_str_id;
    }

    protected function getTime()
    {
        return $this->getParams('time');
    }

    protected function postTweet()
    {
        if($this->accountGet('id') !== false) {
            $tweeting = new Tweeting();

            $tweeting->set([
                'app_key'     => Apps::get($this->getAccount('app'), '_key'),
                'app_secret'  => Apps::get($this->getAccount('app'), '_secret'),
                'user_key'    => $this->getAccount('_key'),
                'user_secret' => $this->getAccount('_secret'),
                'ip'          => Apps::get($this->getAccount('app'), 'ip'),
            ])
                ->post($this->getTweet());

            if($tweeting->getCode() === 200) {
                $this->_str_id = $tweeting->getTweetID();
                return true;
            } else {
                new Errors($this, $tweeting);
                return false;
            }
        } else {
            new Errors($this);
        }
    }

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