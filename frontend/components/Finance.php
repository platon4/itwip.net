<?php

class Finance
{
    static $error;
    static $exchangeRates;

    /*
     * @ int $moneyType (0=real money, 1=bonus money)
     */
    public static function money($amount, $moneyType = 0, $cy = false, $styles = array(), $bill = 0, $elm = true)
    {
        $prefix = "";
        $bill = ($bill) ? $bill : Yii::app()->user->_setting('_preferred_currency');

        $moneyData = self::exchangeRates();

        if($bill AND isset($moneyData[$bill - 1])) {
            $amount = round(($moneyData[$bill - 1]['_calc'] == 0) ? $amount / $moneyData[$bill - 1]['course'] : $amount * $moneyData[$bill - 1]['course'], 2);
            $prefix = Yii::t("internal", "_money_" . $moneyData[$bill - 1]['_vlt']);
        } else {
            $prefix = Yii::t("internal", "_money_0");
        }

        $amount = round((0 + $amount), 2);

        if($cy) {
            $amount = $amount . " " . $prefix;
        }

        if($elm == true) {
            $html = '<span ';

            if($styles)
                $html .= 'style="' . implode("; ", $styles) . '" ';

            if($moneyType == 1) {
                $html .= 'title="' . Yii::t('main', 'title_bonus_money') . '">' . $amount . "Б.";
            } else {
                $html .= '>' . $amount;
            }

            $html .= '</span>';
        } else {
            $html = '';

            if($moneyType == 1)
                $html .= $amount . "Б.";
            else
                $html .= $amount;
        }

        return $html;
    }

    public static function payment($amount, $user_id, $moneyType = 0, $for, $operationData = 0, $notice = '')
    {
        if(!is_numeric($amount) OR !$amount OR Yii::app()->user->isGuest)
            return false;

        if($user_id == Yii::app()->user->id) {
            $money_in_balance = ($moneyType == 1) ? Yii::app()->user->_get('bonus_money') : Yii::app()->user->_get('money_amount');
        } else {
            $row = Yii::app()->db->createCommand("SELECT id,bonus_money,money_amount FROM {{accounts}} WHERE id=:id")->queryRow(true, array(
                ':id' => $user_id));

            if($row === null)
                return false;

            $money_in_balance = ($moneyType == 1) ? $row['money_bonus'] : $row['money_amount'];
        }

        $is_blocked = 0;
        $transfer = 0;

        if($money_in_balance >= $amount) {
            $sql_user = 'UPDATE {{accounts}} SET ';

            if($moneyType == 1)
                $sql_user .= 'bonus_money=bonus_money-' . $amount;
            else
                $sql_user .= 'money_amount=money_amount-' . $amount;

            if(CHelper::int($operationData)) {
                Yii::app()->db->createCommand("INSERT INTO {{money_blocking}} (owner_id,amount,_date,_type,_for,_id,_money_type) VALUES (:owner_id,:amount,:_date,:_type,:_for,:_id,:_money_type)")
                    ->execute(array(
                        ':owner_id'    => $user_id,
                        ':amount'      => $amount,
                        ':_date'       => date("Y-m-d H:i:s"),
                        ':_type'       => 1,
                        ':_for'        => $for,
                        ':_id'         => $operationData,
                        ':_money_type' => $moneyType
                    ));

                $is_blocked = 1;
                $transfer = 1;
            }

            $sql_user .= ' WHERE id=:id';

            if(Yii::app()->db->createCommand($sql_user)->execute(array(
                ':id' => $user_id))
            ) {

                self::_setMoneyLog($amount, $user_id, $moneyType, 1, $is_blocked, $for, $operationData, $notice, $transfer);

                return true;
            } else {
                return false;
            }
        } else {
            self::$error = Yii::t('main', 'insufficient_funds');
            return false;
        }
    }

    /*
     * @ int $moneyType (0=real money, 1=bonus money)
     */

    public static function put($amount, $user_id, $moneyType = 0, $for, $operationData = 0, $moneyLog = true)
    {
        if(!is_numeric($amount) OR !$amount)
            return false;

        $upd = array();

        $is_blocked = 0;

        if(intval($operationData)) {
            Yii::app()->db->createCommand("INSERT INTO {{money_blocking}} (owner_id,amount,_date,_for,_id,_money_type) VALUES (:owner_id,:amount,:_date,:_for,:_id,:_money_type)")
                ->execute(array(
                    ':owner_id'    => $user_id,
                    ':amount'      => $amount,
                    ':_date'       => date("Y-m-d H:i:s"),
                    ':_for'        => $for,
                    ':_id'         => $operationData,
                    ':_money_type' => $moneyType
                ));

            $is_blocked = 1;
        } else {
            if($moneyType == 1)
                $upd[] = 'bonus_money=bonus_money+' . $amount;
            else
                $upd[] = 'money_amount=money_amount+' . $amount;
        }

        if(count($upd))
            Yii::app()->db->createCommand("UPDATE {{accounts}} SET " . implode(", ", $upd) . " WHERE id=:id")->execute(array(
                ':id' => $user_id));

        if($moneyLog)
            self::_setMoneyLog($amount, $user_id, $moneyType, 0, $is_blocked, $for, $operationData);

        return true;
    }

    public static function _getLogNote($type, $for, $operationData, $notice = '')
    {
        $data = array(
            //Доход
            0 => array(
                0 => Yii::t('financeModule.index', '_promo_code_title'),
                1 => Yii::t('financeModule.index', '_system_title_webmoney'),
                2 => Yii::t('financeModule.index', '_system_title_robokassa'),
                3 => Yii::t('financeModule.index', 'income_log_form_ref'),
                4 => Yii::t('financeModule.index', '_checking_tweets_delete', array('{account}' => $notice)),
                5 => Yii::t('financeModule.index', '_checking_tweets_deletion_successfully', array('{account}' => $notice)),
                6 => Yii::t('financeModule.index', '_checking_indexes_delete'),
                7 => Yii::t('financeModule.index', '_checking_indexes_delete_successfully')
            ),
            //Расход
            1 => array(
                0 => Yii::t('financeModule.index', '_providing_collateral_order', array('{id}' => $operationData)),
                1 => Yii::t('financeModule.index', '_out_money'),
                2 => Yii::t('financeModule.index', '_order_executed_successful_completion', array('{id}' => $operationData)),
                3 => Yii::t('financeModule.index', '_checking_tweets_delete_unsuccessfully', array('{account}' => $notice)),
                4 => Yii::t('financeModule.index', '_shop_ref_buy', array('{ref}' => $notice)),

                6 => Yii::t('financeModule.index', '_tweets_indexes_payFromLock', array('{order}' => $notice)),
            ),
            //Возврат
            2 => array(
                0 => Yii::t('financeModule.index', '_removalOrder_return_unspent_funds', array('{id}' => $operationData)), //возврат за заказ
                1 => Yii::t('financeModule.index', 'twitter_money_return_tweet', array('{id}' => $operationData, '{account}' => $notice)), //Возврат за определеный твит с заказа
                3 => Yii::t('financeModule.index', '_checking_tweets_delete_bloger', array('{account}' => $notice)),
                4 => Yii::t('financeModule.index', 'twitter_removeFromTransfer_tweet', ['{id}' => $operationData]),
                5 => Yii::t('financeModule.index', 'twitter_removeFromTransfer', ['{id}' => $operationData]),
                6 => Yii::t('financeModule.index', '_tweet_indexes_checkFail', ['{order}' => $notice]),
                7 => Yii::t('financeModule.index', '_tweet_indexes_checkFailBloger'),
            )
        );

        if(isset($data[$type][$for])) {
            return $data[$type][$for];
        } else
            return Yii::t('financeModule.index', '_undefined_log');
    }

    public static function rePayment($amount, $user_id, $moneyType, $for, $operationID = 0, $operationNotice = '')
    {
        if(!is_numeric($amount) OR !$amount)
            return false;

        $sql_user = 'UPDATE {{accounts}} SET ';

        if($moneyType == 1)
            $sql_user .= 'bonus_money=bonus_money+' . $amount;
        else
            $sql_user .= 'money_amount=money_amount+' . $amount;

        if($operationID) {
            $_b = Yii::app()->db->createCommand("SELECT * FROM {{money_blocking}} WHERE _id=:id LIMIT 1")->queryRow(true, [':id' => $operationID]);

            if($_b !== false) {
                if(($_b['amount'] - $amount) > 0)
                    Yii::app()->db->createCommand("UPDATE {{money_blocking}} SET amount=amount-:money WHERE id=:id")->execute([':id' => $_b['id'], ':money' => $amount]);
                else
                    Yii::app()->db->createCommand("DELETE FROM {{money_blocking}} WHERE id=:id")->execute([':id' => $_b['id']]);
            }
        }

        $sql_user .= ' WHERE id=:id';

        Yii::app()->db->createCommand($sql_user)->execute([':id' => $user_id]);

        self::_setMoneyLog($amount, $user_id, $moneyType, 2, 0, $for, $operationID, $operationNotice, 2);
    }

    /**
     * Insert log operation money
     */
    public static function _setMoneyLog($amount, $user_id, $moneyType, $type, $is_blocked, $for, $operationData = 0, $notice = '', $transfer = 0)
    {
        $values = array(
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
        );

        if($notice) {
            $values['_notice'] = $notice;
        }

        $p = array();
        $f = array();
        $v = array();

        foreach($values as $_k => $_v) {
            $f[] = $_k;
            $v[] = ':' . $_k;
            $p[':' . $_k] = $_v;
        }

        Yii::app()->db->createCommand("INSERT INTO {{money_logs}} (" . implode(",", $f) . ") VALUES (" . implode(",", $v) . ")")
            ->execute($p);
    }

    public function _getError()
    {
        return self::$error;
    }

    protected static function exchangeRates()
    {
        if(self::$exchangeRates === null) {
            $cache = Yii::app()->cache->get('_it_money_course');
            if($cache)
                $cache = unserialize($cache);
            if($cache)
                return $cache;

            self::$exchangeRates = Yii::app()->db->createCommand("SELECT * FROM it_money_course")->queryAll();

            Yii::app()->cache->set('_it_money_course', serialize(self::$exchangeRates));
        }

        return self::$exchangeRates;
    }
}
