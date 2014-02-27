<?php

class SettingsController extends Controller
{
    public $activeMenu = 'accounts';

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
                'actions'=>array('index'),
                'roles'=>array('user'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $form['main']   =mainSettings::model()->findByPk(Yii::app()->user->id);
        $form['minor']  =new minorSettings;
        $form['confirm']=new cSettings;
        $change_password=false;

        if(Yii::app()->user->checkAccess('moderator'))
            $form['minor']->scenario='moderator';

        $confirm=array('email'=>Yii::app()->user->_get('email'),'new_password'=>Yii::app()->user->_get('password'),
            'purse'=>Yii::app()->user->_setting('purse'));

        $form['minor']->attributes=Yii::app()->user->getSettings();
        
        if(isset($_POST['mainSettings']))
        {
            $form['main']->attributes=$_POST['mainSettings'];

            if(isset($_POST['minorSettings']))
                $form['minor']->attributes=$_POST['minorSettings'];

            if(isset($_POST['cSettings']))
            {
                if((isset($_POST['cSettings']['password']) AND trim($_POST['cSettings']['password']) != '') OR isset($_POST['cSettings']['new_password']) AND trim($_POST['cSettings']['new_password']) != '')
                {
                    $change_password          =true;
                    $form['confirm']->scenario='new_password';
                }
                
                $form['confirm']->attributes=$_POST['cSettings'];
            }

            if($form['main']->validate() && $form['minor']->validate() && $form['confirm']->validate())
            {
                if(trim($form['confirm']->email))
                {
                    if($form['confirm']->email != Yii::app()->user->_get('email'))
                    {
                        if(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{accounts}} WHERE email=:email")->queryScalar(array(
                                    ':email'=>$form['confirm']->email)))
                            $form['confirm']->addError('email',Yii::t('accountsModule.settings','_email_exists',array(
                                        '{email}'=>$_POST['cSettings']['email'])));
                    }
                    else
                    {
                        $form['confirm']->addError('email',Yii::t('accountsModule.settings','_email_is_curent_use',array(
                                    '{email}'=>$_POST['cSettings']['email'])));
                    }
                }

                if(!$form['main']->hasErrors() && !$form['minor']->hasErrors() && !$form['confirm']->hasErrors())
                {
                    if(!$change_password OR CHelper::_md5($_POST['cSettings']['password']) == Yii::app()->user->_get('password'))
                    {
                        $message  =array();
                        $message[]=Yii::t('main','_flash_settings_update');

                        if(count($confirm))
                        {
                            $change     =false;
                            $data_change=array();
                            $change_data=array();
                            $change_text=array(
                                'email'=>Yii::t('main','_email'),
                                'new_password'=>Yii::t('main','_password'),
                                'purse'=>Yii::t('main','_purse'),
                            );

                            if($change_password)
                            {
                                $new_password                      =$_POST['cSettings']['new_password'];
                                $_POST['cSettings']['new_password']=CHelper::_md5($_POST['cSettings']['new_password']);
                            }

                            foreach($confirm as $k=> $v)
                            {
                                if(isset($_POST['cSettings'][$k]) AND $_POST['cSettings'][$k] != '' AND $_POST['cSettings'][$k] != $v)
                                {
                                    if($k=='purse' AND trim(Yii::app()->user->_setting('purse'))=='')
                                    {
                                        $form['minor']->purse=$_POST['cSettings'][$k];
                                    } else
                                    {
                                        $change         =true;
                                        $data_change[$k]=$_POST['cSettings'][$k];
                                        $change_data[]  =array('text'=>$change_text[$k],
                                            'value'=>$k == 'new_password'?$new_password:$_POST['cSettings'][$k]);
                                    }
                                }
                            }

                            if($change)
                            {
                                $data_change=@serialize($data_change);
                                $hash       =md5(Yii::app()->user->id.$data_change.CHelper::_getIP());

                                $sth=Yii::app()->db->createCommand("INSERT INTO {{data_change}} (account_id,_data,_date,_hash,_ip) VALUES ('".Yii::app()->user->id."',:data,:date,:hash,:ip) ON DUPLICATE KEY UPDATE _data=:data,_date=:date,_hash=:hash,_ip=:ip");
                                $sth->execute(array(':data'=>$data_change,':date'=>date("Y-m-d H:i:s"),
                                    ':hash'=>$hash,':ip'=>CHelper::_getIP()));

                                $email=Yii::app()->email;

                                $email->to      =$form['main']->email;
                                $email->view    ="_change_data_confirm";
                                $email->viewVars=array('change_data'=>$change_data,
                                    'link'=>Yii::app()->homeUrl.'accounts/confirm_change?_h='.$hash);
                                $email->from    =Yii::app()->params['robot_email'];

                                $email->subject=Yii::t('accountsModule.settings','_change_data_confirm',array(
                                            '{site_title}'=>Yii::app()->name));

                                $email->send();

                                $message[]=Yii::t('accountsModule.settings','_flash_settings_update_confirm',array(
                                            '{email}'=>Yii::app()->user->_get('email')));
                            }
                        }

                        $form['main']->_settings=@serialize($form['minor']->attributes);

                        Yii::app()->user->setFlash("_settings_save_success",implode('<br>',$message));
                        $form['main']->save();
                    } else
                        $form['confirm']->addError('password',Yii::t('accountsModule.settings','_error_invalid_old_password'));
                }
            }
        }

        $this->render('index',array('form'=>$form));
    }

}
