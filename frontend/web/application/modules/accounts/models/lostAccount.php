<?php

class lostAccount extends CActiveRecord
{        
    public $email;
	public $code;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	public function rules()
	{
		return array(
            array('email', 'length', 'max' => 55),
            array('email', 'email', 'message' => Yii::t('index', '_emailInvalid')),
            array('email', 'required', 'message' => Yii::t('index', '_noempty')),
			array('code', 'captcha','allowEmpty'=>false),
		);
	}
    public function tableName()
    {
        return '{{accounts}}';
    }
}
