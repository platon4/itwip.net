<?php

class Subjects extends CActiveRecord
{	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return '{{tw_subjects}}';
    }
	public function _getAll($criteria=array())
	{
		return Subjects::model()->cache(7*86400)->findAll($criteria);
	}	
}
