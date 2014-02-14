<?php

class TwAccounts extends CActiveRecord
{	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	public function rules()
	{
		return array(
			array('screen_name, name, avatar', 'length', 'max' => 255),
		);
	}
    public function tableName()
    {
        return '{{tw_accounts}}';
    }
}
