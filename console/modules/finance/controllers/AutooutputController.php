<?php

/**
 * Description of FinanceCommand
 *
 * @author Александр
 */
#*/2 * * * * php -e /var/www/itwip.net/application/cron.php finance Withdraw

class FinanceCommand extends CConsoleCommand
{
    /*
     * @status: 
     *      1: ожидаеться
     *      2: удачно
     *      3: неудачно
     */

    public function actionInfo($id = 0)
    {
        /*
          include(dirname(__FILE__) . '/wmx/_header.php');

          $w = Yii::app()->db->createCommand("SELECT w.*,a.email,a._settings FROM {{money_withdrawal}} w INNER JOIN {{accounts}} a ON w.owner_id=a.id WHERE w.id=:id")->queryRow(true, array(':id' => $id));

          $res = $wmxi->X3(
          'R398233796434', # номер кошелька для которого запрашивается операция
          0, # номер операции (в системе WebMoney)
          999999998, # номер перевода
          0, # номер счета (в системе WebMoney) по которому выполнялась операция
          0, # номер счета
          date('Ymd H:i:s', strtotime('-1 week')), # минимальное время и дата выполнения операции
          date('Ymd H:i:s', strtotime('+1 day'))          # максимальное время и дата выполнения операции
          );

          print_r($res->Sort());
         */
    }

    public function actionWithdraw()
    {
        include(dirname(__FILE__) . '/wmx/_header.php');

        $w = Yii::app()->db->createCommand("SELECT w.*,a.email,a._settings FROM {{money_withdrawal}} w INNER JOIN {{accounts}} a ON w.owner_id=a.id WHERE w._status=1 LIMIT 10")->queryAll();

        foreach ($w as $m)
        {
            $logs = array();
            $code = 0;
            $status = 3;
            $settings = @unserialize($m['_settings']);

            if (is_array($settings) AND isset($settings['purse']) AND trim($settings['purse']) != '')
            {
                $res = $wmxi->X2(
                        $m['id'], PRIMARY_PURSE, # номер кошелька с которого выполняется перевод (отправитель)
                        $settings['purse'], # номер кошелька, но который выполняется перевод (получатель)
                        round($m['_out'], 2), # переводимая сумма
                        0, # срок протекции сделки в днях
                        '', # код протекции сделки
                        Yii::t('main', '_wm_transfer_notice', array('{acсounts}' => $m['email'])), # описание оплачиваемого товара или услуги
                        0, # номер счета (в системе WebMoney), по которому выполняется перевод
                        1 # учитывать разрешение получателя
                );

                if ($res->ErrorCode() == 0)
                {
                    foreach ($m as $k => $v)
                    {
                        $logs[] = $k . ':' . $v;
                    }
                    $status = 2;
                    $code = 200;
                }
                elseif ($res->ErrorCode() == 103)
                {
                    $status = 2;
                    $code = 200;
                }
                else
                {
                    $code = $res->ErrorCode();
                }
            }
            else
            {
                $code = 1;
                $logs = array('error: не указан кошелек', 'id:' . $m['id']);
            }

            $rowCount = Yii::app()->db->createCommand("UPDATE {{money_withdrawal}} SET _status=:status,_date_execute=:date,_code=:code WHERE id=:id")->execute(array(
                ':date' => date('Y-m-d H:i:s'), ':id' => $m['id'], ':status' => $status,
                ':code' => $code));

            if ($rowCount)
                Yii::app()->db->createCommand("DELETE FROM {{money_blocking}} WHERE _type=1 AND _id=:id")->execute(array(
                    ':id' => $m['id']));

            if ($code == 200)
            {
                $refs_logs = array();
                $loyalty = Yii::app()->db->createCommand("SELECT parent_referral as id,in_balance,loyalty_finance FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true, array(
                    ':id' => $m['owner_id']));

                if ($loyalty['id'] AND Yii::app()->params['extract_precent_system'] == 'on')
                {
                    try
                    {
                        $ref_amount = 0;

                        if ($loyalty['id'] AND Yii::app()->params['extract_precent_system'] == 'on')
                        {
                            $ref = Yii::app()->db->createCommand("SELECT id,status FROM {{accounts}} WHERE id=:id")->queryRow(true, array(
                                ':id' => $loyalty['id']));

                            if ($ref['status'] == 1)
                            {
                                $_amount = $m['_commission'];
                                //$_ref_amount = CMoney::_extractPrecent($_amount, 'referral', $loyalty['id']);
                                //$ref_amount = $_amount - $_ref_amount['amount'];
                                $ref_amount = $_amount - ($_amount - (($_amount * 70) / 100));
                                
                                User::model()->updateCounters(array('money_amount' => +$ref_amount), 'id=' . $loyalty['id']);
                                Finance::_setMoneyLog($ref_amount, $loyalty['id'], 0, 0, 0, 3, $m['owner_id'], $m['email']);

                                Yii::app()->db->createCommand("UPDATE {{loyalty}} SET out_balance=out_balance+:money, brought_user=brought_user+:income WHERE owner_id=:id")->execute(array(
                                    ':id' => $m['owner_id'],
                                    ':money' => $m['_out'],
                                    ':income' => $ref_amount));
                            }
                        }
                    }
                    catch (Exception $ex)
                    {
                        Logs::save("loyalty_error", "Date: " . date('d.m.Y H:i:s') . ";" . $ex . "\n", 'withdraw', 'a+');
                    }
                }

                Finance::_setMoneyLog($m['amount'], $m['owner_id'], 0, 1, 0, 1, $m['id'], '', 3);
                Logs::save("success", "Date: " . date('d.m.Y H:i:s') . ";" . implode('; ', $logs) . "\n", 'withdraw', 'a+');
            }
            else
            {
                try
                {
                    Finance::_setMoneyLog($m['amount'], $m['owner_id'], 0, 1, 1, 1, $m['id'], '', 2);
                    Yii::app()->db->createCommand("UPDATE {{accounts}} SET money_amount=money_amount+:money WHERE id=:id")->execute(array(
                        ':id' => $m['owner_id'], ':money' => $m['amount']));
                }
                catch (Exception $ex)
                {
                    Logs::save("error_back", "Date: " . date('d.m.Y H:i:s') . ";" . implode('; ', $logs) . "\n", 'withdraw', 'a+');
                }

                Logs::save("error", "Date: " . date('d.m.Y H:i:s') . ";" . implode('; ', $logs) . "\n", 'withdraw', 'a+');
            }

            sleep(1);
        }
    }

    public function actionrepay($id)
    {
        /*
          if(!$id) die('Invalid Id');

          include(dirname(__FILE__) . '/wmx/_header.php');

          $m = Yii::app()->db->createCommand("SELECT w.*,a.email,a._settings FROM {{money_withdrawal}} w INNER JOIN {{accounts}} a ON w.owner_id=a.id WHERE w.id=:id")->queryRow(true,array(':id'=>$id));
          $settings = unserialize($m['_settings']);

          if (is_array($settings) AND isset($settings['purse']) AND trim($settings['purse']) != '')
          {
          $res = $wmxi->X2(
          999999998, //last id
          PRIMARY_PURSE, # номер кошелька с которого выполняется перевод (отправитель)
          $settings['purse'], # номер кошелька, но который выполняется перевод (получатель)
          round($m['_out'], 2), # переводимая сумма
          0, # срок протекции сделки в днях
          '', # код протекции сделки
          Yii::t('main', '_wm_transfer_notice', array('{acсounts}' => $m['email'])), # описание оплачиваемого товара или услуги
          0, # номер счета (в системе WebMoney), по которому выполняется перевод
          1 # учитывать разрешение получателя
          );

          if ($res->ErrorCode() == 0)
          {
          foreach ($m as $k => $v)
          {
          $logs[] = $k . ':' . $v;
          }
          $status = 2;
          echo "success\n";
          }
          elseif ($res->ErrorCode() == 103)
          {
          $status = 2;
          echo "error: operation aleardy\n";
          }
          else
          {
          $code = $res->ErrorCode();
          echo "error: ".$code."\n";
          }
          }
          else
          {
          $code = 1;
          echo  'error: не указан кошелек';
          }
         * 
         */
    }
}
