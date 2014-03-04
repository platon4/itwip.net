<?php

class lostPassword extends CActiveRecord
{
    public $acc_id;
    public $lost_key;
    public $date;
    public $ip;

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{lost_password}}';
    }
}
