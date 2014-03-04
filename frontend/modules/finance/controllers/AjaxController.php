<?php

class ajaxController extends Controller {

    public function init()
    {
        parent::init();

        if(!Yii::app()->request->isAjaxRequest)
            throw new CHttpException('403','Url should be requested via ajax only.');
    }

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
                'actions'=>array('_promo','autowithdraw','withdraw'),
                'roles'=>array('user'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionAutoWithdraw()
    {
        $form=new autoWithdraw;
        $code=99;
        
        if($form->validate())
            $code=200;
        
        echo json_encode(array('html'=>'<div style="margin-top: 7px;" class="line_info alert">Данная функция временно недоступна.</div>'));
        Yii::app()->end();
    }

    public function actionWithdraw()
    {
        $w   =new Withdraw;
        $code=0;

        $w->attributes=isset($_POST['_o'])?$_POST['_o']:array();
        $w->_purse    =Yii::app()->user->_setting('purse');

        if($w->validate())
            $code=200;

        echo json_encode(array('code'=>$code,'msg'=>$w->getError()));
        Yii::app()->end();
    }

    public function action_promo()
    {
        $code=isset($_POST['_code'])?trim($_POST['_code']):false;

        if($code)
        {
            //Проверяем если пользователь не привысил лимит ввода неверного кода
            $sth     =Yii::app()->db->createCommand("SELECT _count FROM {{promo_attempts}} WHERE owner_id=:owner_id");
            $rowCount=$sth->queryRow(true,array(':owner_id'=>Yii::app()->user->id));

            if($rowCount['_count'] <= 2)
            {
                //Проверяем если пользователь использовал за последние 24 часа промокод
                $command=Yii::app()->db->createCommand("SELECT  COUNT(*) as count FROM {{promo_code_logs}} WHERE _owner_use=:_owner_use AND _date_use>:date");
                $logsRow=$command->queryRow(true,array(':_owner_use'=>Yii::app()->user->id,
                    ':date'=>date("Y-m-d H:i:s",time() - (31 * 86400))));

                if(!$logsRow['count'])
                {
                    //Проверяем есть ли введеный промокод
                    $command=Yii::app()->db->createCommand("SELECT * FROM {{promo_code}} WHERE _hash_code=:_hash_code");
                    $row    =$command->queryRow(true,array(':_hash_code'=>md5($code)));

                    if($row['_hash_code'])
                    {
                        if($row['_hash_code'] != '2f44d265798274e7efb684d348ed756f')
                        {
                            echo json_encode(array('code'=>208,'message'=>Yii::t('financeModule.index','_promo_action_is_over')));
                            Yii::app()->end();
                        }

                        $parent_ref=Yii::app()->db->createCommand("SELECT parent_referral FROM {{loyalty}} WHERE owner_id=:id")->queryScalar(array(
                            ':id'=>Yii::app()->user->id));

                        if($row['_tie'] != Yii::app()->user->id)
                        {
                            if((!$parent_ref OR !$row['_tie']) OR ($parent_ref == $row['_tie']))
                            {
                                if(($row['_use_count'] < $row['_count']) OR ($row['_count'] == 0))
                                {
                                    if(!Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{promo_code_logs}} WHERE _ip=:ip")->queryScalar(array(
                                                ':ip'=>CHelper::_getIP())))
                                    {
                                        try
                                        {
                                            $transaction=Yii::app()->db->beginTransaction();

                                            //Удаляем неудачные попытки ввода кода
                                            Yii::app()->db->createCommand("DELETE FROM {{promo_attempts}} WHERE owner_id=:owner_id")
                                                    ->execute(array(':owner_id'=>Yii::app()->user->id));

                                            //Записаваем в логи использование промо кода
                                            Yii::app()->db->createCommand("INSERT INTO {{promo_code_logs}} (_hash_code,_code,_owner_use,_date_use,_amount,_ip) VALUES (:_hash_code,:_code,:_owner_use,:_date_use,:_amount,:_ip)")
                                                    ->execute(array(':_hash_code'=>$row['_hash_code'],
                                                        ':_code'=>$row['_code'],
                                                        ':_owner_use'=>Yii::app()->user->id,
                                                        ':_date_use'=>date("Y-m-d H:i:s"),
                                                        ':_amount'=>$row['_amount'],
                                                        ':_ip'=>CHelper::_getIP()));

                                            if($row['_type'] == 1)
                                            {
                                                //Ставим пользователя который использовал промокод, в рефералы пользователя которому привязан промо код.
                                                Yii::app()->db->createCommand("UPDATE {{loyalty}} SET parent_referral=:parent_referral WHERE owner_id=:owner")
                                                        ->execute(array(':owner'=>Yii::app()->user->id,
                                                            ':parent_referral'=>$row['_tie']));

                                                Yii::app()->db->createCommand("UPDATE {{promo_code}} SET _use_count=_use_count+1 WHERE _hash_code=:id")
                                                        ->execute(array(':id'=>$row['_hash_code']));

                                                if($row['_tie'])
                                                {
                                                    $count        =Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{loyalty}} l INNER JOIN {{accounts}} a ON l.owner_id=a.id WHERE l.parent_referral=:id AND a.status=1")->queryScalar(array(
                                                        ':id'=>$row['_tie']));
                                                    $owner_loyalty=Yii::app()->db->createCommand("SELECT loyalty_referral FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true,array(
                                                        ':id'=>$row['_tie']));

                                                    $ref_steps    =LoyaltyHelper::_getData('referral');
                                                    $ref_next_step=explode('-',$ref_steps[$owner_loyalty['loyalty_referral'] + 1][1]);

                                                    if($count + 1 >= $ref_next_step[0])
                                                    {
                                                        if($owner_loyalty['loyalty_referral'] < count($ref_steps) - 1)
                                                        {
                                                            Yii::app()->db->createCommand("UPDATE {{loyalty}} SET loyalty_referral=:loyalty_ref WHERE owner_id=:id")->execute(array(
                                                                ':id'=>$row['_tie'],
                                                                ':loyalty_ref'=>$owner_loyalty['loyalty_referral'] + 1));
                                                        }
                                                    }
                                                }
                                            } else
                                            {
                                                //Удаляем промо код, если промо код одноразовый.
                                                Yii::app()->db->createCommand("DELETE FROM {{promo_code}} WHERE _hash_code=:_hash_code")
                                                        ->execute(array(':_hash_code'=>md5($code)));
                                            }

                                            //Зачисляем на счет пользователя
                                            Finance::put($row['_amount'],Yii::app()->user->id,1,0);

                                            //Записаваем в логи использование промо кода
                                            Yii::app()->db->createCommand("INSERT INTO {{money_replenishmentit}} (owner_id,amount,_date,_time,_add_to_balance,is_pay) VALUES (:owner_id,:momney,:_date,:_time,:momney,1)")
                                                    ->execute(array(
                                                        ':owner_id'=>Yii::app()->user->id,
                                                        ':momney'=>$row['_amount'],
                                                        ':_date'=>date("Y-m-d"),
                                                        ':_time'=>date("H:i:s")
                                            ));

                                            $transaction->commit();

                                            echo json_encode(array('code'=>200,'message'=>Yii::t('financeModule.index','_code_use_success')));
                                        } catch(Exception $e)
                                        {
                                            $transaction->rollBack();
                                            echo json_encode(array('code'=>206,'message'=>Yii::t('financeModule.index','_code_use_error_system')));
                                        }
                                    } else
                                    {
                                        echo json_encode(array('code'=>209,'message'=>Yii::t('financeModule.index','_code_use_count_limit_ip')));
                                    }
                                } else
                                {
                                    echo json_encode(array('code'=>207,'message'=>Yii::t('financeModule.index','_code_use_count_limit')));
                                }
                            } else
                            {
                                echo json_encode(array('code'=>208,'message'=>Yii::t('financeModule.index','_no_use_code_is_ref')));
                            }
                        } else
                        {
                            echo json_encode(array('code'=>208,'message'=>Yii::t('financeModule.index','_no_use_code_is_owner_promo_ref')));
                        }
                    } else
                    {
                        //Записаваем неудачиную попытку ввода кода
                        Yii::app()->db->createCommand("INSERT INTO {{promo_attempts}} (owner_id,_ip,_date) VALUES (:owner_id, :_ip, :date) ON DUPLICATE KEY UPDATE _count=_count+1, _date=:date")->execute(array(
                            ':date'=>date("Y-m-d H:i:s"),':owner_id'=>Yii::app()->user->id,
                            ':_ip'=>CHelper::_getIP()));

                        echo json_encode(array('code'=>404,'message'=>Yii::t('financeModule.index','_code_not_found')));
                    }
                } else
                    echo json_encode(array('code'=>503,'message'=>Yii::t('financeModule.index','_day_code_use_limit_exceeded')));
            } else
                echo json_encode(array('code'=>502,'message'=>Yii::t('financeModule.index','_query_limit_exceeded')));
        } else
            echo json_encode(array('code'=>403,'message'=>Yii::t('financeModule.index','_enter_promo_code')));

        Yii::app()->end();
    }

}
