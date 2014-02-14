<?php

class newAccount extends FormModel {

    public $_all;
    public $name;
    public $email;
    public $password;
    public $agreed;
    public $code;
    protected $activationKey;
    protected $resendKey;
    protected $_id;
    public function rules()
    {
        return array(
            array('code','captcha','allowEmpty'=>false,'on'=>'captcha'),
            array('password','length','min'=>3),
            array('email','length','max'=>55),
            array('name','length','min'=>2),
            array('name','length','max'=>255),
            array('email','email','message'=>Yii::t('index','_emailInvalid')),
            array('email','_emailExists'),
            array('name','match','pattern'=>'/^[A-Za-z-А-Яа-я\s]+$/u','message'=>Yii::t('index','_no_valid_name')),
            
            array('name','required','message'=>Yii::t('index','_noempty')),
            array('email','required','message'=>Yii::t('index','_noempty')),
            array('password','required','message'=>Yii::t('index','_noempty')),
            
            array('agreed','compare','compareValue'=>true,'message'=>Yii::t('index','_agreed')),
            array('agreed','_allowNew'),
            array('_all','unsafe'),
        );
    }

    public function _allowNew()
    {
        if(Yii::app()->params['allowNewAccounts'] != 'yes')
        {
            $this->clearErrors();
            $this->addError('agreed',Yii::t('accountsModule.accounts','_create_account_not_allowed'));
        }
    }

    public function _emailExists()
    {
        if(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{accounts}} WHERE email=:email")->queryScalar(array(
                    ':email'=>$this->email)))
        {
            $this->addError('email',Yii::t('accountsModule.accounts','_email_is_exists'));
        }
    }

    public function createAccount()
    {
        try
        {
            $t=Yii::app()->db->beginTransaction();

            Yii::app()->db->createCommand("INSERT INTO {{accounts}} (email,password,name,_ip,_date_create) VALUES (:email,:password,:name,:_ip,:_date_create)")
                    ->execute(array(
                        ':email'=>$this->email,
                        ':password'=>CHelper::_md5($this->password),
                        ':name'=>$this->name,
                        ':_ip'=>CHelper::_getIP(),
                        ':_date_create'=>date('Y-m-d H:i:s')
            ));

            $this->_id=Yii::app()->db->getLastInsertID();

            $this->createLoyalty($this->_id);

            $this->activationKey=CHelper::_md5(time().$this->password.$this->_id.$this->email.time());
            $this->resendKey    =CHelper::_md5($this->email.$this->password.date("d"));

            Yii::app()->db->createCommand("INSERT INTO {{accounts_activation}} (acc_id,resend_key,activation_key) VALUES (:acc_id,:resend_key,:activation_key)")
                    ->execute(array(
                        'acc_id'=>$this->_id,
                        'resend_key'=>$this->resendKey,
                        'activation_key'=>$this->activationKey,
            ));

            $this->integrationWithForum(); //Интеграция с другими системами

            $t->commit();
            return true;
        } catch(Exception $ex)
        {
            $this->addError('_all',Yii::t('accountsmodule.accouints','_system_error_create_account'));
            $t->rollBack();
            return false;
        }
    }

    public function sendActivationMail()
    {
        $email=Yii::app()->email;

        $email->to      =$this->email;
        $email->view    ="_activation";
        $email->viewVars=array('mail'=>$this->email,'password'=>$this->password,
            'link'=>Yii::app()->homeUrl.'accounts/service/activation?_k='.$this->activationKey);
        $email->from    =Yii::app()->params['robot_email'];

        $email->subject=Yii::t('accountsModule.accounts','_email_subject',array(
                    '{site_title}'=>Yii::app()->name));

        $email->send();
    }

    protected function createLoyalty($id)
    {
        $_code          =CHelper::_gString(5,15);
        $parent_referral=0;

        if(isset(Yii::app()->session['_referral_code']))
        {
            $pRef=Yii::app()->db->createCommand("SELECT owner_id FROM {{loyalty}} WHERE _code_hash=:hash")->queryScalar(array(
                ':hash'=>md5(Yii::app()->session['_referral_code'])));

            if($pRef)
            {
                $parent_referral=$pRef;
            }
        }

        Yii::app()->db->createCommand("INSERT INTO {{loyalty}} (owner_id,_code_hash,_code,parent_referral) VALUES (:owner_id,:_code_hash,:_code,:parent_referral)")
                ->execute(array(
                    ':owner_id'=>$id,
                    ':_code_hash'=>md5($_code),
                    ':_code'=>$_code,
                    ':parent_referral'=>$parent_referral,
        ));
    }

    public function _getResendKey()
    {
        return $this->resendKey;
    }

    /*
     * Интеграция с другими системами
     */
    protected function integrationWithForum()
    {
        $login=substr(md5($this->email.time()),0,16);
        
        Yii::app()->fdb->createCommand("INSERT INTO {{users}} (id,login,email,nickname,password,is_locked,regdate) VALUES (:id,:login,:email,:nickname,:password,:is_locked,:regdate)")
                ->execute(array(
                    ':id'=>$this->_id,
                    ':login'=>$login,
                    ':email'=>$this->email,
                    ':nickname'=>$this->name,
                    ':password'=>CHelper::_md5($this->password),
                    ':is_locked'=>0,
                    ':regdate'=>date('Y-m-d H:i:s'),
        ));
        
        Yii::app()->fdb->createCommand("INSERT INTO {{user_profiles}} (user_id) VALUES (:user_id)")
                ->execute(array(
                    ':user_id'=>$this->_id,
        ));
    }

}
