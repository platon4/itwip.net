<?php

class Support extends CActiveRecord
{	
	public $verifyCode;
	public $_email;
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function rules()
	{
		return array(		
			array('_email','safe', 'on'=>'guest'),
            array('_email', 'required', 'message' => Yii::t('index', '_noempty'), 'on'=>'guest'),
            array('_email', 'length', 'max' => 55,'on'=>'guest'),
            array('_email', 'email', 'message' => Yii::t('index', '_emailInvalid'),'on'=>'guest'),
			array('verifyCode','captcha','allowEmpty'=>!CCaptcha::checkRequirements(),'on'=>'guest'),
			
			array('_subject,_text,_to','safe'),
            array('_subject, _text', 'required', 'message' => Yii::t('index', '_noempty')),
            array('_to', 'in', 'allowEmpty'=>false,'range'=>array(0,1,2,3), 'message' => Yii::t('index', '_select_support_to')),
		);
	}
	
	public function tableName()
	{
		return '{{tickets}}';
	}
}