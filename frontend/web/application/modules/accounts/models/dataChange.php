<?php

class dataChange extends CActiveRecord
{
	public $id;
	public $email;
	public $status;
	public $password;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
    public function tableName()
    {
        return '{{data_change}}';
    }
}