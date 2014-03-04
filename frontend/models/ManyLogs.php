<?php

class ManyLogs extends CActiveRecord
{	
	public $customErrors=array();

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return '{{money_logs}}';
    }
	
    /**
     */
    public function _addError($attribute, $error) {
        $this->customErrors[] = array($attribute, $error);
    }
	
    /**
     */
    protected function beforeValidate() {
        $r = parent::beforeValidate();

        foreach ($this->customErrors as $param) {
            $this->addError($param[0], $param[1]);
        }
        return $r;
    }	
}
