<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Manual implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    protected $days = [];
    protected $hours = [];

    public function make()
    {
        if($this->getProcessDate() <= date('Y-m-d')) {

        }
    }

    public function getProcessDate()
    {
        $params = $this->getParam('targeting');

        if(isset($params['t']) && is_array($params['t'])) {

        } else {

        }
    }

    public function getInterval()
    {
        if($this->_interval === null) {
            $params = $this->getParam('targeting');

            if(isset($params['targeting']['interval']) && $params['targeting']['interval'] >= 30) {
                $this->_interval = rand($params['targeting']['interval'] - 10, $params['targeting']['interval'] + 10);
            } else {
                $this->_interval = rand(20, 40);
            }
        }

        return $this->_interval;
    }
}