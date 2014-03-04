<?php

class CMainStats extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'tbl_tag':
	 * @var integer $id
	 * @var string $name
	 * @var integer $frequency
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{accounts}}';
	}
	
}