<?php

namespace console\modules\twitter\components\tweeting;

use Yii;
use yii\base\Exception;
use common\api\twitter\Accounts;
use common\api\twitter\Apps;
use console\modules\twitter\actions\TweetingErrors;
use console\modules\twitter\components\Tweeting;
use yii\db\Query;

class Indexes implements TweetingInterface
{
    use TweetingTrait;

    protected $_account;

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
        if($this->postTweet() === true) {

            echo 'test';
            die();
            try {
                $t = Yii::app()->db->beginTransaction();

                Yii::$app->db->createCommand()->insert('{{%twitter_tweetingAccountsLogs}}', ['account_id' => $this->accountGet('id'), 'logType' => 'indexes'])->execute();
                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }
        }
    }

    protected function postTweet()
    {
        if($this->accountGet('id') !== false) {
            $tw = new Tweeting();

            $tw->set([
                'app_key'     => Apps::get($this->accountGet('app'), '_key'),
                'app_secret'  => Apps::get($this->accountGet('app'), '_secret'),
                'user_key'    => $this->accountGet('_key'),
                'user_secret' => $this->accountGet('_secret')
            ])
                ->post($this->getTweet());

            if($tw->getCode() === 200) {
                return true;
            } else {
                new TweetingErrors($this->getTask(), $tw);
                return false;
            }
        } else {
            new TweetingErrors($this->getTask(), 'notAccount');
        }
    }

    protected function accountGet($key)
    {
        if($this->_account === null) {
            $this->_account = (new Accounts())->where(['and', 'in_indexses=1', 'in_yandex=1', ['not exists', (new Query())->select('id')->from('{{%twitter_tweetingAccountsLogs}}')->where(['logType' => 'indexes', 'account_id' => 'a.id'])]])->one();

            if($this->_account === false)
                $this->_account = (new Accounts())->where(['and', 'in_indexses=1', 'in_yandex=1'])->one();
        }

        return isset($this->_account[$key]) ? $this->_account[$key] : false;
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