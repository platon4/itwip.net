<?php

namespace common\api\finance;

use Yii;
use yii\base\Exception;
use yii\db\Query;

class Operation
{
    public static $_transactionTypes = [
        'income'      => 0,
        'consumption' => 1,
        'return'      => 2
    ];

    public static $_transactionAccrued = [
        0 => [
            'promo'               => 0,
            'webmoney'            => 1,
            'robokassa'           => 2,
            'refferal'            => 3,
            'tweetsCheck'         => 4,
            'tweetsCheckSuccess'  => 5,
            'indexesCheck'        => 6,
            'indexesCheckSuccess' => 7,
        ],
        1 => [
            'guaranty'                 => 0,
            'moneyOut'                 => 1,
            'orderSuccessfulExecuted'  => 2,
            'tweetCheckUnsuccessfully' => 3,
            'tweetsCheckSuccess'       => 7,
            'buyReferral'              => 4,
            // - 6
            'indexesCheckSuccess'      => 7
        ],
        2 => [
            'removeOrder'              => 0,
            'removeTweet'              => 1,
            'bloggerDeletedTweet'      => 3,
            'indexesFail'              => 6,
            'indexesFailBloger'        => 7,
            'errorPostTweet'           => 8,
            'tweetCheckUnsuccessfully' => 9,
        ]
    ];

    public static $_moneyType = [
        'purse' => 0,
        'bonus' => 1
    ];

    protected static function accrued($transactionType, $accrued)
    {
        $a = isset(self::$_transactionAccrued[$transactionType][$accrued]) ? self::$_transactionAccrued[$transactionType][$accrued] : false;

        if($a !== false)
            return $a;
        else
            throw new Exception('Invalid accrued.');
    }

    protected static function transactionType($t)
    {
        $transaction = isset(self::$_transactionTypes[$t]) ? self::$_transactionTypes[$t] : false;

        if($transaction !== false)
            return $transaction;
        else
            throw new Exception('Invalid transaction type.');
    }

    protected static function moneyType($m)
    {
        $money = isset(self::$_moneyType[$m]) ? self::$_moneyType[$m] : false;

        if($money !== false)
            return $money;
        else
            throw new Exception('Invalid money type.');
    }

    protected static function amountValid($amount)
    {
        if(is_numeric($amount) && $amount > 0)
            return true;
        else
            throw new Exception('Invalid amount.');
    }

    public static function put($amount, $user_id, $moneyType, $accrued, $operationData = 0, $notice = '', $moneyLog = true)
    {
        self::amountValid($amount);
        $moneyType = self::moneyType($moneyType);
        $accrued = self::accrued(0, $accrued);

        $upd = array();

        $is_blocked = 0;

        if(intval($operationData)) {
            Yii::$app->db->createCommand("INSERT INTO {{%money_blocking}} (owner_id,amount,_date,_for,_id,_money_type) VALUES (:owner_id,:amount,:_date,:_for,:_id,:_money_type)", [
                ':owner_id'    => $user_id,
                ':amount'      => $amount,
                ':_date'       => date("Y-m-d H:i:s"),
                ':_for'        => $accrued,
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
            Yii::$app->db->createCommand("UPDATE {{%accounts}} SET " . implode(", ", $upd) . " WHERE id=:id", [':id' => $user_id])->execute();

        if($moneyLog)
            self::log($amount, $user_id, $moneyType, 0, $is_blocked, $accrued, $operationData, $notice);

        return true;
    }

    public static function unlockMoney($amount, $return_amount, $user_id, $adv_id, $moneyType, $accrued, $operationID, $order_id, $notice = '')
    {
        self::amountValid($amount);
        $moneyType = self::moneyType($moneyType);

        if($moneyType == 1)
            $columns = 'bonus_money=bonus_money+' . $amount;
        else
            $columns = 'money_amount=money_amount+' . $amount;

        self::moneyLockUpdate($amount, $operationID, $user_id);
        self::moneyLockUpdate($return_amount, $order_id, $adv_id);

        Yii::$app->db->createCommand('UPDATE {{%accounts}} SET ' . $columns . ' WHERE id=:id', [':id' => $user_id])
            ->execute();

        self::log($amount, $user_id, $moneyType, 0, 0, self::accrued(0, $accrued), $operationID, $notice, 2);
        self::log($return_amount, $adv_id, $moneyType, 1, 0, self::accrued(1, $accrued), $order_id, '', 3);
    }

    public static function returnMoney($amount, $user_id, $moneyType, $accrued, $operationID = 0, $operationNotice = '')
    {
        self::amountValid($amount);
        $moneyType = self::moneyType($moneyType);
        $accrued = self::accrued(2, $accrued);

        if($moneyType == 1)
            $columns = 'bonus_money=bonus_money+' . $amount;
        else
            $columns = 'money_amount=money_amount+' . $amount;

        self::moneyLockUpdate($amount, $operationID, $user_id);

        Yii::$app->db->createCommand('UPDATE {{%accounts}} SET ' . $columns . ' WHERE id=:id', [':id' => $user_id])->execute();

        self::log($amount, $user_id, $moneyType, 2, 0, $accrued, $operationID, $operationNotice, 2);
    }

    public static function cancelTransfer($amount, $user_id, $moneyType, $accrued, $operationID, $notice = '')
    {
        self::amountValid($amount);
        $moneyType = self::moneyType($moneyType);
        $accrued = self::accrued(2, $accrued);

        self::moneyLockUpdate($amount, $operationID, $user_id);

        self::log($amount, $user_id, $moneyType, 2, 0, $accrued, $operationID, $notice, 3);
    }

    /*
     * Запись денежной операций в логи
     */
    public static function log($amount, $user_id, $moneyType, $type, $is_blocked, $for, $operationData = 0, $notice = '', $transfer = 0)
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

    protected static function moneyLockUpdate($amount, $operationID, $user_id)
    {
        if($operationID) {
            $command = Yii::$app->db->createCommand();

            $_b = (new Query())
                ->from('{{%money_blocking}}')
                ->where(['owner_id' => $user_id, '_id' => $operationID])
                ->one();

            if($_b !== false) {
                if(($_b['amount'] - $amount) > 0)
                    Yii::$app->db->createCommand('UPDATE {{%money_blocking}} SET amount=amount-:money WHERE id=:id', [':id' => $_b['id'], ':money' => $amount])
                        ->execute();
                else
                    $command->delete('{{%money_blocking}}', 'id=:id', [':id' => $_b['id']])->execute();
            }
        }
    }
}