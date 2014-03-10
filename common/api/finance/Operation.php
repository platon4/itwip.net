<?php

namespace common\api\finance;

use Yii;
use yii\db\Query;

class Operation
{
    public static function put($amount, $user_id, $moneyType = 0, $for, $operationData = 0, $moneyLog = true)
    {
        if(!is_numeric($amount) OR !$amount)
            return false;

        $upd = array();

        $is_blocked = 0;

        if(intval($operationData)) {
            Yii::$app->db->createCommand("INSERT INTO {{%money_blocking}} (owner_id,amount,_date,_for,_id,_money_type) VALUES (:owner_id,:amount,:_date,:_for,:_id,:_money_type)")
                ->bindValues([
                    ':owner_id'    => $user_id,
                    ':amount'      => $amount,
                    ':_date'       => date("Y-m-d H:i:s"),
                    ':_for'        => $for,
                    ':_id'         => $operationData,
                    ':_money_type' => $moneyType
                ])
                ->execute();

            $is_blocked = 1;
        } else {
            if($moneyType == 1)
                $upd[] = 'bonus_money=bonus_money+' . $amount;
            else
                $upd[] = 'money_amount=money_amount+' . $amount;
        }

        if(count($upd))
            Yii::$app->db->createCommand("UPDATE {{%accounts}} SET " . implode(", ", $upd) . " WHERE id=:id")->bindValues([':id' => $user_id])->execute();

        if($moneyLog)
            self::log($amount, $user_id, $moneyType, 0, $is_blocked, $for, $operationData);

        return true;
    }

    public static function returnMoney($amount, $user_id, $moneyType, $for, $operationID = 0, $operationNotice = '')
    {
        if(!is_numeric($amount) OR !$amount)
            return false;

        $command = Yii::$app->db->createCommand();

        if($moneyType == 1)
            $columns = 'bonus_money=bonus_money+' . $amount;
        else
            $columns = 'money_amount=money_amount+' . $amount;

        if($operationID) {
            $_b = (new Query())
                ->from('{{%money_blocking}}')
                ->where(['_id' => $operationID])
                ->one();

            if($_b !== false) {
                if(($_b['amount'] - $amount) > 0)
                    Yii::$app->db->createCommand('UPDATE {{%money_blocking}} SET amount=amount-:money WHERE id=:id')->bindValues([':id' => $_b['id'], ':money' => $amount])->execute();
                else
                    $command->delete('{{%money_blocking}}', 'id=:id', [':id' => $_b['id']])->execute();
            }
        }

        Yii::$app->db->createCommand('UPDATE {{%accounts}} SET ' . $columns . ' WHERE id=:id')->bindValues([':id' => $user_id])->execute();

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