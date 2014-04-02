<?php

namespace common\api\finance;

use common\api\accounts\Accounts;
use yii\console\Exception;

class Money
{
    public static function amount($amount, $precentSystem = null, $id = 0)
    {
        if($precentSystem !== null) {
            if(!is_numeric($id) || $id <= 0)
                throw(new Exception('sum() - Not set id in class ' . get_called_class()));

            $precent = Accounts::getPrecent($precentSystem, $id);
            $amount = $amount - ($amount - (($amount * $precent) / 100));
        }

        return $amount;
    }
} 