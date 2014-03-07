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
        $this->init();

        $this->setTask();
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