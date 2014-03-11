<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Indexes implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    protected $_tasks;

    public function make()
    {
        $this->setTasks();
    }

    public function processTask($task)
    {
        $this->_task[] = [
            'order_id'     => $this->get('id'),
            'sbuorder_id'  => $task['id'],
            'orderType'    => 'indexes',
            'tweet_hash'   => $task['hash'],
            'url_hash'     => $task['url_hash'],
            'process_time' => date('H:i:s'),
            'params'       => $this->getTaskParams($task)
        ];

        $this->_update['task'][$task['id']]['is_process'] = 1;
    }

    public function getTaskParams($data)
    {
        return json_encode([
            'tweet'         => $this->_getTaskParams('tweet'),
            'account'       => $this->_getTaskParams('account'),
            'order_owner'   => $this->get('owner_id'),
            'amount'        => $data['cost'],
            'return_amount' => $data['return_amount'],
            'interval'      => 0
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
        }
    }
}