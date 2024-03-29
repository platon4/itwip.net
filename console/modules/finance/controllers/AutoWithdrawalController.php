<?php

namespace console\modules\finance\controllers;

use common\api\finance\Money;
use common\api\finance\Operation;
use console\components\Logger;
use Yii;
use yii\base\Exception;
use yii\db\Query;

class AutoWithdrawalController extends \console\components\Controller
{
    public function actionPay()
    {
        $this->writeln('AutoPay init');

        require(dirname(__DIR__) . '/libraries/wmx/_header.php');
        $command = Yii::$app->db->createCommand();

        $pays = (new Query())
            ->select('w.*,a.email,a._settings')
            ->from('{{%money_withdrawal}} w')
            ->innerJoin('{{%accounts}} a', 'w.owner_id=a.id')
            ->where('w._status=1')
            ->limit(10)
            ->all();

        foreach($pays as $pay) {
            $this->writeln('Order id ' . $pay['id'] . ' start process.');
            $code = 0;
            $status = 3;

            try {
                $this->writeln('Init transaction');
                $t = Yii::$app->db->beginTransaction();

                if($settings = unserialize($pay['_settings'])) {
                    $this->writeln('Settings init success');

                    if(is_array($settings) AND isset($settings['purse']) AND trim($settings['purse']) != '') {
                        $res = $wmxi->X2(
                            $pay['id'],
                            PRIMARY_PURSE, # номер кошелька с которого выполняется перевод (отправитель)
                            $settings['purse'], # номер кошелька, но который выполняется перевод (получатель)
                            round($pay['_out'], 2), # переводимая сумма
                            0, # срок протекции сделки в днях
                            '', # код протекции сделки
                            'iTwip.net, вывод средств пользователю ' . $pay['email'] . '. Нам будет приятно если Вы оставите отзыв о сайте, перейдя по ссылке: http://advisor.wmtransfer.com/sitedetails.aspx?url=itwip.net', # описание оплачиваемого товара или услуги
                            0, # номер счета (в системе WebMoney), по которому выполняется перевод
                            1 # учитывать разрешение получателя
                        );

                        if($res->ErrorCode() == 0 || $res->ErrorCode() == 103) {
                            $status = 2;
                            $code = 200;
                        } else {
                            $code = $res->ErrorCode();
                        }

                        $this->writeln('Response code from webmoney ' . $res->ErrorCode());

                        if($code === 200) {
                            /** Реферальная система*/

                            $this->updatePayOrder($pay, $status, $code);

                            $loyalty = (new Query())
                                ->select('parent_referral as id,in_balance,loyalty_finance')
                                ->from('{{%loyalty}}')
                                ->where(['owner_id' => $pay['owner_id']])
                                ->one();

                            if($loyalty !== false) {
                                $reff = (new Query())
                                    ->select('id,status')
                                    ->from('{{%accounts}}')
                                    ->where(['id' => $loyalty['id']])
                                    ->one();

                                if($reff !== false && $reff['status'] == 1) {
                                    $reffAmount = Money::amount($pay['_commission'], 'refferal', $loyalty['id']);

                                    Yii::$app->db->createCommand("UPDATE {{%accounts}} SET money_amount=money_amount+:amount WHERE id=:id", [
                                        ':id'     => $loyalty['id'],
                                        ':amount' => $reffAmount,
                                    ])
                                        ->execute();

                                    Yii::$app->db->createCommand("UPDATE {{%loyalty}} SET out_balance=out_balance+:money, brought_user=brought_user+:income WHERE owner_id=:id", [
                                        ':id'     => $pay['owner_id'],
                                        ':money'  => $pay['_out'],
                                        ':income' => $reffAmount
                                    ])
                                        ->execute();

                                    /** Записаваем в логи пользователя доход с рефералла */
                                    Operation::log($reffAmount, $loyalty['id'], 0, 0, 0, 3, $pay['owner_id'], $pay['email']);
                                }
                            }

                            /** Записаваем вывод средств в логи пользователя */
                            Operation::log($pay['amount'], $pay['owner_id'], 0, 1, 0, 1, $pay['id'], '', 3);

                            /** Записаваем транзакцию в логи */
                            Logger::error('Success pay', array_merge($pay, ['code' => $code]), 'finance/pays', 'autoPay');
                        } else {
                            Logger::error('Error pay 1', array_merge($pay, ['code' => $code]), 'finance/errors', 'autoPayResponse');
                        }
                    } else {
                        Logger::error('Error not set purse', $pay, 'finance/errors', 'autoPay');
                    }
                } else {
                    Logger::error('Error unserialize settings', $pay, 'finance/errors', 'autoPay');
                }

                if($code !== 200)
                    $this->cancelPay($pay, $status, $code);

                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
                Logger::error($e, array_merge($pay, ['code' => $code]), 'finance/errors', 'autoPayException');
            }

            sleep(3);
        }
    }

    public function updatePayOrder($pay, $status, $code)
    {
        $command = Yii::$app->db->createCommand();

        $rowCount = $command->update('{{%money_withdrawal}}', ['_status' => $status, '_date_execute' => date('Y-m-d H:i:s'), '_code' => $code], ['id' => $pay['id']])
            ->execute();

        if($rowCount)
            $command->delete('{{%money_blocking}}', ['_type' => '1', '_id' => $pay['id']])
                ->execute();
    }

    public function cancelPay($pay, $status, $code)
    {
        Yii::$app->db->createCommand("UPDATE {{%accounts}} SET money_amount=money_amount+:money WHERE id=:id", [
            ':id'     => $pay['owner_id'],
            ':amount' => $pay['amount'],
        ])
            ->execute();

        $this->updatePayOrder($pay, $status, $code);

        Operation::log($pay['amount'], $pay['owner_id'], 0, 1, 1, 1, $pay['id'], '', 2);
    }

    public function writeln($msg)
    {
        echo $msg . PHP_EOL;
    }
} 