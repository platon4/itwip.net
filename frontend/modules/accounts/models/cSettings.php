<?php

class cSettings extends CFormModel
{	
	public $email;
	public $password='';
	public $new_password='';
	public $purse;
	
	public function rules()
	{
		return array(
			array('email,password,new_password,purse','safe'),
			array('email','email'),
			array('password','required','message'=>Yii::t('accountsModule.settings','_error_empty_old_password'),'on'=>'new_password'),
			array('new_password','required','message'=>Yii::t('accountsModule.settings','_error_empty_new_password'),'on'=>'new_password'),
			array('new_password','length','min'=>3,'tooShort'=>Yii::t('accountsModule.settings','_error_lenght_new_password'),'on'=>'new_password'),
			array('purse','match','pattern'=>'/R[0-9]{12}/','message'=>Yii::t('accountsModule.settings','_error_invalid_wmr_purse')),
		);
	}
}