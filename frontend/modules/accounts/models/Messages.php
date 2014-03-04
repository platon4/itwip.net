<?php

class Messages extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	public function rules()
	{
		return array();
	}
    public function tableName()
    {
        return '{{messages}}';
    }
}