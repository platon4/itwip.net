<?php

namespace common\api\finance;

use Yii;
use yii\db\Query;

class Operation
{
    public static function returnMoney($amount, $user_id, $moneyType, $for, $operationID = 0, $operationNotice = '')
    {
        if(!is_numeric($amount) OR !$amount)
            return false;

        $command = Yii::$app->db->createCommand();

        if($moneyType == 1)
            $columns = ['bonus_money' => 'bonus_money+' . $amount];
        else
            $columns = ['money_amount' => 'money_amount + ' . $amount];

        if($operationID) {
            $_b = (new Query())
                ->from('{{%money_blocking}}')
                ->where(['_id' => $operationID])
                ->one();

            if($_b !== false) {
                if(($_b['amount'] - $amount) > 0)
                    $command->update('{{%money_blocking}}', ['amount' => 'amount-:money'], 'id=:id', [':id' => $_b['id'], ':money' => $amount])->execute();
                else
                    $command->delete('{{%money_blocking}}', 'id=:id', [':id' => $_b['id']])->execute();
            }
        }

        $command->update('{{%accounts}}', $columns, 'id=:id', [':id' => $user_id]);

        self::log($amount, $user_id, $moneyType, 2, 0, $for, $operationID, $operationNotice, 2);
    }

    /*
     * Запись денежной операций в логи
     */
    private static function log($amount, $user_id, $moneyType, $type, $is_blocked, $for, $operationData = 0, $notice = '', $transfer = 0)
    {
        $command = Yii::$app->db->createCommand();
        $columns = [
            'owner_id'    => $user_id,
            '_type'       => $type, //операция (доход/расход/возврат)
            '_system'     => $for, //примичяние операций
            '_date'       => date("Y-m-d"),
            '_time'       => date("H:i:s"),
            '_amount'     => $amount,
            'order_id'    => $operationData,
            'amount_type' => $moneyType, //тип валюты
            'is_blocked'  => $is_blocked,
            '_transfer'   => $transfer,
        ];

        if($notice)
            $columns['_notice'] = $notice;

        $command->insert('{{%money_logs}}', $columns)->execute();
    }
}