<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Manual implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    public function make()
    {
        if($this->getProcessDate() <= date('Y-m-d')) {

        }
    }

    public function getProcessDate()
    {
        $params = $this->getParam('targeting');

        if(isset($params['t'])) {
            print_r($params['t']);
        }
    }
}