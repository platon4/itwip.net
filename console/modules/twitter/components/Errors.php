<?php

namespace console\modules\twitter\components;

use Yii;
use console\components\Logger;
use common\api\finance\Operation;
use yii\db\Query;

class Errors
{
    private $_errors = [
        188 => 'removeMalwareTweet',
        89  => 'invalidExpiredToken',
        64  => 'accountIsSuspended',
    ];

    private $_code = 0;
    private $_error;

    public function errorTweetPost($model, $tweeting)
    {
        $this->processResponse($tweeting->getResult());

        if(isset($this->_errors[$this->getErrorCode()]) && method_exists($this, $this->_errors[$this->getErrorCode()])) {
            $method = $this->_errors[$this->getErrorCode()];
            $this->$method($model);
        } else {
            $this->unknownError($model);
        }

        Logger::error($tweeting->getResult(), $model->getTask(), 'daemons/tweeting/tweets', 'errorPostTweet');
    }

    public function getErrorCode()
    {
        return $this->_code;
    }

    public function getErrorMessage()
    {
        return $this->_error;
    }

    public function processResponse($response)
    {
        if(isset($response['errors'][0])) {
            if(isset($response['errors'][0]['code']))
                $this->_code = $response['errors'][0]['code'];

            if(isset($response['errors'][0]['code']))
                $this->_error = $response['errors'][0]['message'];
            else
                $this->_error = 'Unknown response from twitter';
        }
    }

    public function processTask($model, $message)
    {
        $command = Yii::$app->db->createCommand();

        $pay_type = $model->get('payment_type') == 0 ? 'purse' : 'bonus';

        Operation::returnMoney($model->get('adv_amount'), $model->getOwner(), $pay_type, 'errorPostTweet', $model->get('order_id'), $model->get('sbuorder_id'));

        if($model->get('orderType') == 'indexes' || $model->get('orderType') == 'manual') {
            $status = '4';
        } else {
            $status = '9';
        }

        $command->delete('{{%twitter_tweeting}}', ['id' => $model->get('id')])->execute();
        $command->update('{{%twitter_ordersPerform}}', ['message' => $message, 'status' => $status], ['id' => $model->get('sbuorder_id')])->execute();

        if($model->get('order_hash') !== null) {
            $count = (new Query())
                ->from('{{%twitter_ordersPerform}}')
                ->where(['and', 'order_hash=:hash', ['or', 'status=0', 'status=1']], [':hash' => $model->get('order_hash')])
                ->count();

            if($count == 0)
                $command->update('{{%twitter_orders}}', ['status' => '3'], ['id' => $model->get('order_id')])->execute();
        }

        Logger::error('', ['id' => $model->get('id'), 'message' => $message, 'status' => $status, 'sub_id' => $model->get('sbuorder_id')], 'daemons/tweeting/logs', 'process');
    }

    public function unknownError($model)
    {
        $command = Yii::$app->db->createCommand();

        try {
            $t = Yii::$app->db->beginTransaction();
            $this->processTask($model, 'Не удалось обработать задание, неопределенная ошибка.');

            $t->commit();
        } catch(Exception $e) {
            Logger::error($e, [], 'daemons/tweeting/errors', 'Errors-unknownError');
            $t->rollBack();
        }
    }

    /**
     * Выполняем при ошибки с кодом 188 (твит содержит вредусную ссылку, твиттер отверг твит)
     */
    public function removeMalwareTweet($model)
    {
        $command = Yii::$app->db->createCommand();

        try {
            $t = Yii::$app->db->beginTransaction();

            $this->processTask($model, $this->getErrorMessage());

            $t->commit();
        } catch(Exception $e) {
            Logger::error($e, [], 'daemons/tweeting/errors', 'Errors-removeMalwareTweet');
            $t->rollBack();
        }
    }

    /**
     * Выполняем при ошибки с кодом 89 (Нету доступа к аккаунту)
     */
    public function invalidExpiredToken($model)
    {
        $command = Yii::$app->db->createCommand();

        try {
            $t = Yii::$app->db->beginTransaction();

            $command->update('{{%tw_accounts}}', ['_status' => '4'], ['id' => $model->getAccount('id')])->execute();
            $this->processTask($model, $this->getErrorMessage());
            $this->flush($model);

            $t->commit();
        } catch(Exception $e) {
            Logger::error($e, [], 'daemons/tweeting/errors', 'Errors-invalidExpiredToken');
            $t->rollBack();
        }
    }

    public function accountNotFound($model)
    {
        $command = Yii::$app->db->createCommand();

        try {
            $t = Yii::$app->db->beginTransaction();

            $this->processTask($model, 'Account was deleted from the system');
            $this->flush($model);

            $t->commit();
        } catch(Exception $e) {
            Logger::error($e, [], 'daemons/tweeting/errors', 'Errors-accountNotFound');
            $t->rollBack();
        }
    }

    /**
     * Выполняем при ошибки с кодом 64 (Учетная запись временно приостановлена)
     */
    public function accountIsSuspended($model)
    {
        $command = Yii::$app->db->createCommand();

        try {
            $t = Yii::$app->db->beginTransaction();

            $command->update('{{%tw_accounts}}', ['_status' => '3'], ['id' => $model->getAccount('id')])->execute();
            $this->processTask($model, $this->getErrorMessage());
            $this->flush($model);

            $t->commit();
        } catch(Exception $e) {
            Logger::error($e, [], 'daemons/tweeting/errors', 'Errors-accountIsSuspended');
            $t->rollBack();
        }
    }

    public function flush($model)
    {
        Yii::$app->redis->hDel('twitterAccounts', $model->getAccount('id'));
    }
}