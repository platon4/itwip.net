<?php

class PayController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('success', 'fail'),
                'roles'   => array('user'),
            ),
            array('allow',
                'actions' => array('robaresult', 'webmoneyresult'),
                'roles'   => array('guest'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /*
     * Робокасса
     */

    public function actionRobaresult()
    {
        $mrh_pass2 = "v5*6-45v65wret45-6*4-564245-6";

        //установка текущего времени
        //current date
        $tm = getdate(time() + 9 * 3600);
        $date = "$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

        // чтение параметров
        // read parameters
        $out_summ = $_REQUEST["OutSum"];
        $inv_id = $_REQUEST["InvId"];
        $shp_item = $_REQUEST["Shp_item"];
        $crc = $_REQUEST["SignatureValue"];

        $crc = strtoupper($crc);

        $my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));

        if($my_crc == $crc) {
            $form = Replenishment::model()->findByPk($inv_id);

            if(count($form)) {
                if(!$form->is_pay) {
                    if(!$this->addMoneyToBalance($form, 2)) {
                        echo "system error\n";
                    } else {
                        echo "OK$inv_id\n";
                    }

                    $f = @fopen(Yii::app()->getModule("finance")->getBasePath() . "/logs/robokassa.txt", "a+") or die("error");
                    fputs($f, "User_id:$shp_item; order_num:$inv_id; Summ:$out_summ; Date:$date\n");
                    fclose($f);
                } else
                    echo "order is pay\n";
            } else
                echo "bad order\n";
        } else
            echo "bad sign\n";

        Yii::app()->end();
    }

    public function actionSuccess()
    {
        $title = Yii::t('main', '_info');
        $message = Yii::t('financeModule.index', '_info_pay_succes');

        $this->render('application.views.main.info', array('title' => $title, 'message' => $message,
                                                           'link'  => '/finance/replenishment'));

        Yii::app()->end();
    }

    public function actionFail()
    {
        $title = Yii::t('main', '_error');
        $message = Yii::t('financeModule.index', '_info_pay_fail');

        $this->render('application.views.main.info', array('title' => $title, 'message' => $message,
                                                           'link'  => '/finance/replenishment'));

        Yii::app()->end();
    }

    /**
     * Webmoney
     */
    public function actionWebmoneyresult()
    {
        $pay_id = (isset($_POST['TTS_PAY']) AND intval($_POST['TTS_PAY'])) ? intval($_POST['TTS_PAY']) : 0;

        if(isset($_POST['LMI_PREREQUEST']) AND $_POST['LMI_PREREQUEST'] == 1) {
            $form = Replenishment::model()->findByPk($pay_id);

            if(count($form)) {
                if(!$form->is_pay) {
                    echo "YES";
                } else
                    echo "The bill has already been paid.";
            } else
                echo "Account to pay for was not found.";

            echo "Account to pay for was not found.";
        } else {
            $secret_key = 'd5"0m-0-/0,0&/3ergs-55-c3-*54/6';

            $lmi_payee_purse = isset($_POST['LMI_PAYEE_PURSE']) ? $_POST['LMI_PAYEE_PURSE'] : "";
            $lmi_payment_amount = isset($_POST['LMI_PAYMENT_AMOUNT']) ? $_POST['LMI_PAYMENT_AMOUNT'] : "";
            $lmi_payment_no = isset($_POST['LMI_PAYMENT_NO']) ? $_POST['LMI_PAYMENT_NO'] : "";
            $lmi_mode = isset($_POST['LMI_MODE']) ? $_POST['LMI_MODE'] : "";
            $lmi_sys_invs_no = isset($_POST['LMI_SYS_INVS_NO']) ? $_POST['LMI_SYS_INVS_NO'] : "";
            $lmi_sys_trans_no = isset($_POST['LMI_SYS_TRANS_NO']) ? $_POST['LMI_SYS_TRANS_NO'] : "";
            $lmi_sys_trans_date = isset($_POST['LMI_SYS_TRANS_DATE']) ? $_POST['LMI_SYS_TRANS_DATE'] : "";
            $lmi_payer_purse = isset($_POST['LMI_PAYER_PURSE']) ? $_POST['LMI_PAYER_PURSE'] : "";
            $lmi_payer_wm = isset($_POST['LMI_PAYER_WM']) ? $_POST['LMI_PAYER_WM'] : "";

            $common_string = $lmi_payee_purse . $lmi_payment_amount . $lmi_payment_no . $lmi_mode . $lmi_sys_invs_no . $lmi_sys_trans_no . $lmi_sys_trans_date . $secret_key . $lmi_payer_purse . $lmi_payer_wm;
            $hash = strtoupper(md5($common_string));

            $f = @fopen(Yii::app()->getModule("finance")->getBasePath() . "/logs/test.txt", "a+") or die("error");
            fputs($f, var_export($_POST, true));
            fclose($f);

            if(isset($_POST['LMI_HASH']) AND $hash == $_POST['LMI_HASH']) {
                $form = Replenishment::model()->findByPk($pay_id);
                $tm = getdate(time() + 9 * 3600);
                $date = "$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

                echo 'Test';
                die();
                if(count($form)) {
                    if(!$form->is_pay) {
                        if($form->amount == trim($_POST['LMI_PAYMENT_AMOUNT'])) {
                            if(!$this->addMoneyToBalance($form, 1)) {
                                echo "System error.";
                            }

                            $f = @fopen(Yii::app()->getModule("finance")->getBasePath() . "/logs/webmoney.txt", "a+") or die("error");
                            fputs($f, "User_id:$form->owner_id; order_num:$form->id;Summ:$form->_add_to_balance; Date:$date\n");
                            fclose($f);
                        } else
                            echo "Incorrect amount of replenishment.";
                    } else
                        echo "The bill has already been paid.";
                } else
                    echo Yii::t('yii', 'Your request is invalid, invalid operation ID.');
            } else
                echo Yii::t('yii', 'Your request is invalid, invalid operation data.');
        }

        Yii::app()->end();
    }

    private function addMoneyToBalance(&$form, $type = 0)
    {
        User::model()->updateCounters(array('money_amount' => +$form->_add_to_balance), 'id=' . $form->owner_id);

        $form->is_pay = 1;
        $form->_date = date("Y-m-d");
        $form->_time = date("H:i");
        $form->save();

        $loyalty = Yii::app()->db->createCommand("SELECT parent_referral as id,in_balance,loyalty_finance FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true, array(
            ':id' => $form->owner_id));

        $f_steps = LoyaltyHelper::_getData('finance');
        $left_to_f_next_step = $f_steps[$loyalty['loyalty_finance'] + 1][1];
        $loyalty_finance = 0;

        if(($loyalty['in_balance'] + $form->_add_to_balance) >= $left_to_f_next_step) {
            if($loyalty['loyalty_finance'] < count($f_steps) - 1) {
                $loyalty_finance = 1;
            }
        }

        Yii::app()->db->createCommand("INSERT INTO {{money_logs}} (owner_id,_type,_system,_date,_time,_amount) VALUES (:owner_id,:_type,:_system,:_date,:_time,:_amount)")->execute(array(
                ':owner_id' => $form->owner_id,
                ':_type'    => 0,
                ':_system'  => $type,
                ':_date'    => date("Y-m-d"),
                ':_time'    => date("H:i:s"),
                ':_amount'  => $form->_add_to_balance,
            )
        );

        $ref_amount = 0;

        if($loyalty['id'] AND Yii::app()->params['extract_precent_system'] == 'on') {
            $ref = Yii::app()->db->createCommand("SELECT id,status FROM {{accounts}} WHERE id=:id")->queryRow(true, array(
                ':id' => $loyalty['id']));

            if($ref['status'] == 1) {
                $_amount = $form->amount - $form->_add_to_balance;
                // $_ref_amount=CMoney::_extractPrecent($_amount,'referral',$loyalty['id']);
                //$ref_amount =$_amount - $_ref_amount['amount'];

                $ref_amount = $_amount - ($_amount - (($_amount * 70) / 100));

                User::model()->updateCounters(array('money_amount' => +$ref_amount), 'id=' . $loyalty['id']);

                Yii::app()->db->createCommand("INSERT INTO {{money_logs}} (owner_id,_type,_system,_date,_time,_amount) VALUES (:owner_id,:_type,:_system,:_date,:_time,:_amount)")->execute(array(
                    ':owner_id' => $loyalty['id'],
                    ':_type'    => 0,
                    ':_system'  => 3,
                    ':_date'    => date("Y-m-d"),
                    ':_time'    => date("H:i:s"),
                    ':_amount'  => $ref_amount,
                ));
            }
        }

        Yii::app()->db->createCommand("UPDATE {{loyalty}} SET in_balance=in_balance+:money, brought_user=brought_user+:income,loyalty_finance=loyalty_finance+:loyalt_finance WHERE owner_id=:id")->execute(array(
            ':loyalt_finance' => $loyalty_finance, ':id' => $form->owner_id, ':money' => $form->_add_to_balance,
            ':income'         => $ref_amount));

        return true;
    }

    public function actionWresult()
    {
        $this->render('application.views.main.info', array('title'   => Yii::t('main', '_error'),
                                                           'message' => Yii::t('financeModule.index', '_info_pay_fail'), 'link' => '/finance/replenishment'));
    }
}
