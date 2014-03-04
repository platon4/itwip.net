<?php

class Accounts extends CActiveRecord
{
    public $customErrors = [];

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{tw_accounts}}';
    }

    public function rules()
    {
        return array(
            array('_status', 'safe'),
        );
    }

    public function relations()
    {
        return array(
            'settings' => array(self::HAS_ONE, 'Settings', 'tid')
        );
    }

    public function _addError($attribute, $error)
    {
        $this->customErrors[] = array($attribute, $error);
    }

    protected function beforeValidate()
    {
        parent::beforeValidate();

        foreach($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }

        return true;
    }
}
