<?php

class ServiceController extends Controller {

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
                'actions'=>array('created','lostpassword','activation'),
                'users'=>array('?'),
            ),
            array('allow',
                'actions'=>array('logout'),
                'users'=>array('@'),
            ),
            array('deny',
                'users'=>array('@'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionCreated($_k)
    {
       $this->redirect('/accounts/created?_k='.$_k); 
    }
    
    /**
     * Восстановление пароля
     */
    public function actionLostPassword($send=null,$_k=null)
    {
        if(isset($_k) AND strlen($_k) == 32)
        {
            $lost=lostPassword::model()->find('lost_key=?',array($_k));

            if($lost->id)
            {
                $accLost=lostAccount::model()->find('id=?',array($lost->acc_id));

                $password=$this->_getPassword();

                $email=Yii::app()->email;

                $email->to      =$accLost->email;
                $email->view    ="_lost_password";
                $email->viewVars=array('type'=>'','password'=>$password,'mail'=>$accLost->email,
                    'ip'=>CHelper::_getIP());
                $email->from    =Yii::app()->params['robot_email'];

                $email->subject=Yii::t('accountsModule.accounts','_email_lost_subject',array(
                            '{site_title}'=>Yii::app()->name));

                $email->send();

                lostAccount::model()->updateByPk($accLost->id,array('password'=>CHelper::_md5($password)));
                $lost->delete();

                $this->render('application.views.main.info',array('title'=>Yii::t('accountsModule.accounts','_info_lost_reset'),
                    'message'=>Yii::t('accountsModule.accounts','_email_lost_reset'),
                    'link'=>'/'));
            } else
            {
                throw new CHttpException(404,Yii::t('accountsModule.accounts','_lost_key_error'));
            }
        } else
        {
            if(Yii::app()->user->isGuest)
            {
                $this->render('application.views.main.info',array('title'=>Yii::t('accountsModule.accounts','_info_lost_reset'),
                    'message'=>Yii::t('accountsModule.accounts','_email_lost_send'),
                    'link'=>'/','link_screen'=>Yii::t('main','__main')));
            } else
            {
                throw new CHttpException(403,Yii::t('accounts','_error_key_create'));
            }
        }
    }

    public function actionActivation($_k)
    {
        $row=strlen($_k) == 32?Yii::app()->db->createCommand("SELECT * FROM {{accounts_activation}} WHERE activation_key=:activation_key LIMIT 1")->queryRow(true,array(':activation_key'=>$_k)):false;

        if($row!==false)
        {
            $acc_obj=Account::model()->find('id=?',array($row['acc_id']));

            if($acc_obj->id AND $acc_obj->status == 0)
            {
                $loyalty=Yii::app()->db->createCommand("SELECT * FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true,array(
                    ':id'=>$acc_obj->id));

                if($loyalty['parent_referral'])
                {
                    $count        =Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{loyalty}} l INNER JOIN {{accounts}} a ON l.owner_id=a.id WHERE l.parent_referral=:id AND a.status=1")->queryScalar(array(
                        ':id'=>$loyalty['parent_referral']));
                    $owner_loyalty=Yii::app()->db->createCommand("SELECT loyalty_referral FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true,array(
                        ':id'=>$loyalty['parent_referral']));

                    $ref_steps    =LoyaltyHelper::_getData('referral');
                    $ref_next_step=explode('-',$ref_steps[$owner_loyalty['loyalty_referral'] + 1][1]);

                    if($count + 1 >= $ref_next_step[0])
                    {
                        if($owner_loyalty['loyalty_referral'] < count($ref_steps) - 1)
                        {
                            Yii::app()->db->createCommand("UPDATE {{loyalty}} SET loyalty_referral=:loyalty_ref WHERE owner_id=:id")->execute(array(
                                ':id'=>$loyalty['parent_referral'],':loyalty_ref'=>$owner_loyalty['loyalty_referral'] + 1));
                        }
                    }
                }

                Account::model()->updateByPk($row['acc_id'],array('status'=>1));
                Yii::app()->db->createCommand("DELETE FROM {{accounts_activation}} WHERE id=:id")->execute(array(':id'=>$row['id']));

                $this->render('application.views.main.info',array('title'=>Yii::t('accountsModule.accounts','_info_account_is_activation'),
                    'message'=>Yii::t('accountsModule.accounts','_account_is_activation'),
                    'link'=>'/accounts/auth','link_screen'=>Yii::t('accountsModule.accounts','_auth')));
            } else
            {
                throw new CHttpException(404,Yii::t('accountsModule.accounts','_error_key_create'));
            }
        } else
            throw new CHttpException(404,Yii::t('accountsModule.accounts','_activation_key_error'));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    private function _getPassword($length=10)
    {
        $chars=array_merge(range(0,9),range('a','z'),range('A','Z'));

        shuffle($chars);

        return implode(array_slice($chars,0,$length));
    }

}
