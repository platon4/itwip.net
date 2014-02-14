<?php

class minorSettings extends CFormModel
{
	public $system_news_administration;
	public $system_expire_premium_subscription;
	public $system_new_orders_fwebmaster;	
	
	public $email_new_private;
	public $email_attemps_notification;
	public $email_new_orders_fwebmaster;
	
	public $icq_new_private;
	public $icq_attemps_notification;
	public $icq_new_orders_fwebmaster;
	
	public $_preferred_currency;
	public $_language;
	public $_icq;
	public $purse;

	public $email_new_snotification;
	public $icq_new_snotification;
        
	public function rules()
	{
		return array(
			array('_icq,email_new_orders_fwebmaster,system_new_orders_fwebmaster,icq_new_orders_fwebmaster,icq_attemps_notification,icq_new_private,system_news_administration,system_expire_premium_subscription,email_new_private,email_attemps_notification,mail_new_orders_fwebmaster,_preferred_currency','safe'),
			
			array('icq_new_snotification,email_new_snotification','safe','on'=>'moderator'),
			array('icq_new_snotification,email_new_snotification','numerical','integerOnly'=>true,'min'=>0,'max'=>1,'on'=>'moderator'),
			
			array('system_news_administration,system_expire_premium_subscription,email_new_private,email_attemps_notification','numerical','integerOnly'=>true,'min'=>0,'max'=>1),
			array('_icq','numerical','integerOnly'=>true,'message'=>Yii::t('accountsModule.settings','_error_invalid_icq')),
			array('_preferred_currency','in','range'=>array(0)),
			array('_language','in','range'=>array(0)),
		);
	}
}