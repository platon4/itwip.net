<?php

class mainSettings extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

	public function rules()
	{
		return array(
			array('_allow_ip,name','safe'),
			array('_allow_ip','application.extensions.ipvalidator.IPValidator', 'version' => 'subnetwork'),
			array('name','length', 'allowEmpty'=>true,'max' => '200', 'tooLong'=>Yii::t('accountsModule.settings','_error_name_is_long')),
			array('name','default','value'=>Yii::app()->user->_get('name')),
		);
	} 
	
	public function tableName()
	{
		return '{{accounts}}';
	}
}