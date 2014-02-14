<?php

class Account extends CActiveRecord
{        
    public $email;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return '{{accounts}}';
    }
}
