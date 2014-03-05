<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Indexes implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    public function getUpdate()
    {
        return $this->_updates;
    }

    public function getTaks()
    {
        return $this->_taks;
    }
}