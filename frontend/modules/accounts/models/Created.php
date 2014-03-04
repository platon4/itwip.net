<?php

/**
 * Description of Created
 *
 * @author Александр
 */
class Created extends FormModel {

    public $key;
    public $email;
    public $lastResend;
    protected $status;
    protected $data=false;

    public function rules()
    {
        return array(
            array('key','_key'),
        );
    }

    public function afterValidate()
    {
        if(!$this->hasErrors())
        {
            $this->data=$this->_getData();

            if($this->data !== false)
            {
                $this->status    =$this->data['status'];
                $this->email     =$this->data['email'];
                $this->lastResend=$this->data['last_resend'];
            } else
            {
                $this->addError('key',Yii::t('accountsModule.accounts','_activation_key_not_found'));
            }
        }
    }

    public function _key()
    {
        if(strlen($this->key) != 32)
        {
            $this->addError('key',Yii::t('main','invalid_request'));
        }
    }

    public function timeOut()
    {
        if($this->lastResend != '' AND $this->lastResend > (time() - (15 * 60)))
        {
            return false;
        } else
        {
            return true;
        }
    }

    public function isActivation()
    {
        if($this->status == 0)
        {
            return false;
        } else
        {
            return true;
        }
    }

    public function _getData()
    {
        if($this->data===false)
        {
            return Yii::app()->db->createCommand("SELECT a.id as account_id, a.email, a.password, a.status, c.id, c.resend_key, c.activation_key, c.acc_id, c.last_resend FROM {{accounts_activation}} c INNER JOIN {{accounts}} a ON a.id=c.acc_id WHERE c.resend_key=:key LIMIT 1")->queryRow(true,array(
                        ':key'=>$this->key));
        }else {
            return $this->data;
        }
    }

    public function reSendActivationMail()
    {
        $this->data=$this->_getData();
        
        $email=Yii::app()->email;

        $email->to  =$this->email;
        $email->from=Yii::app()->params['robot_email'];

        $email->view    ="_resend";
        $email->viewVars=array('link'=>Yii::app()->homeUrl.'accounts/service/activation?_k='.$this->data['activation_key']);

        $email->subject=Yii::t('accountsModule.accounts','_email_subject',array(
                    '{site_title}'=>Yii::app()->name));

        $email->send();

        Yii::app()->db->createCommand("UPDATE {{accounts_activation}} SET last_resend=:time WHERE resend_key=:key")->execute(array(
            ':time'=>time(),':key'=>$this->key));
    }

}
