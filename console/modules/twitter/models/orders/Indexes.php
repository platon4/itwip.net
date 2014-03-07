<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Indexes implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    public function make()
    {
    }

    public function processTask($task)
    {
        $this->_task[] = [
            'order_id'     => $this->get('id'),
            'sbuorder_id'  => $task['id'],
            'orderType'    => 'indexes',
            'tweet_hash'   => $task['hash'],
            'url_hash'     => $task['url_hash'],
            'process_time' => $this->getTaskProcessTime($task),
            'params'       => $this->getTaskParams($task)
        ];
    }

    public function getTasks()
    {
        return (new Query())
            ->select('*')
            ->from('{{%twitter_ordersPerform}}')
            ->where(['order_hash' => $this->get('order_hash'), 'is_process' => 0, 'status' => 0])
            ->limit($this->getLimit())
            ->all();
    }
}