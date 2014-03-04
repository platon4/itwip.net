<?php

class Tickets extends CActiveRecord
{

    public $text;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            array('admin_read', 'safe'),
            array('text', 'required', 'message' => 'Введите сообщения для пользователя.', 'on' => 'new'),
            array('text', 'length', 'max' => 3000, 'tooLong' => 'Сообщения слишком длинное, максимальное количество символов 3000.', 'on' => 'new'),
        );
    }

    public function tableName()
    {
        return '{{tickets}}';
    }
}
