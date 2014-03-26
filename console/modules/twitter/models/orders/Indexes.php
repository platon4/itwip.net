<?php

namespace console\modules\twitter\models\orders;

use common\helpers\Url;
use Yii;
use common\api\twitter\Accounts;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Indexes implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    protected $_tasks;

    public function make()
    {
        $this->setTasks();

        $this->_update['order'][$this->get('id')]['is_process'] = 1;
    }

    public function processTask($task)
    {
        $acc_id = $this->getAccountID();

        $this->_task[] = [
            'order_id'      => $this->get('id'),
            'order_hash'    => $this->get('order_hash'),
            'sbuorder_id'   => $task['id'],
            'orderType'     => 'indexes',
            'tweet_hash'    => $task['hash'],
            'domen'         => Url::getDomen($task['url']),
            'tw_account'    => $acc_id,
            'process_time'  => date('H:i:s'),
            'payment_type'  => $this->get('payment_type'),
            'params'        => $this->getTaskParams($task),
            'daemon'        => $this->getDaemon(),
            'bloger_amount' => $task['cost'],
            'adv_amount'    => $task['return_amount'],
        ];

        $this->_update['task'][$task['id']]['is_process'] = 1;
    }

    public function getAccountID()
    {
        $account = $this->getAccountQuery();

        if($account === false) {
            Yii::$app->db->createCommand()->delete('{{%twitter_tweetingAccountsLogs}}', ['logType' => 'indexes'])->execute();
            $account = $this->getAccountQuery();
        }

        if(isset($account['id']))
            Yii::$app->db->createCommand()->insert('{{%twitter_tweetingAccountsLogs}}', ['account_id' => $account['id'], 'logType' => 'indexes'])->execute();

        return isset($account['id']) ? $account['id'] : false;
    }

    /**
     * Запрос для получение аккаунта
     *
     * @return array|bool
     */
    protected function getAccountQuery()
    {
        $account = (new Accounts())
            ->where([
                'and',
                'a._status=1',
                's.in_indexses=1',
                ['not exists', (new Query())->select('id')->from('{{%twitter_tweetingAccountsLogs}}')->where(['and', 'logType=\'indexes\'', 'account_id=a.id'])]
            ])
            ->one();

        return $account;
    }

    public function getTaskParams($data)
    {
        return json_encode([
            'tweet'         => $data['tweet'],
            'order_owner'   => $this->get('owner_id'),
            'url'           => $data['url'],
            'time'          => $this->_getTaskParams('time'),
            'interval'      => 0,
        ]);
    }

    public function getTasks()
    {
        if($this->_tasks === null) {
            $this->_tasks = (new Query())
                ->select('*')
                ->from('{{%twitter_ordersPerform}}')
                ->where(['order_hash' => $this->get('order_hash'), 'is_process' => 0, 'status' => 0])
                ->all();
        }

        return $this->_tasks;
    }

    protected function setTasks()
    {
        $tasks = $this->getTasks();

        if(!empty($tasks)) {
            foreach($tasks as $task) {
                $this->_setTaskParams($task);
                $this->processTask($task);
            }
        } else {
            $this->_update['order'][$this->get('id')]['status'] = 2;
        }
    }
}