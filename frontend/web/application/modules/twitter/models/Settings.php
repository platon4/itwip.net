<?php

class Settings extends CActiveRecord
{
	public $customErrors = array();

	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{tw_accounts_settings}}';
	}

	public function rules()
	{
		return array(
			array('_gender, working_in, fast_posting, allow_retweet, allow_following, _age, _filter, _status, _stop,_allow_bonus_pay', 'safe'),
			array('_price', 'numerical', 'integerOnly' => false, 'min' => CMoney::_c(0.5), 'max' => CMoney::_c(100000), 'tooSmall' => Yii::t('twitterModule.accounts', '_price_is_small', array('{price}' => CMoney::_c(0.5, true))), 'tooBig' => Yii::t('twitterModule.accounts', '_price_is_big', array('{price}' => CMoney::_c(100000, true)))),
			array('_timeout', 'numerical', 'integerOnly' => true, 'min' => Yii::app()->params['twitter']['posting_timeout'], 'max' => Yii::app()->params['twitter']['posting_timeout_max'], 'tooSmall' => Yii::t('twitterModule.accounts', '_timeout_is_small', array('{time}' => Yii::app()->params['twitter']['posting_timeout'])), 'tooBig' => Yii::t('twitterModule.accounts', '_timeout_is_big', array('{time}' => Yii::app()->params['twitter']['posting_timeout_max']))),
			array('_filter', 'length', 'allowEmpty' => false, 'min' => 2, 'max' => 2000, 'on' => 'filter'),
		);
	}

	public function relations()
	{
		return array(
			'accounts' => array(self::HAS_ONE, 'Accounts', 'id')
		);
	}

	public function _addError($attribute, $error)
	{
		$this->customErrors[] = array($attribute, $error);
	}

	protected function beforeValidate()
	{
		$r = parent::beforeValidate();

		foreach($this->customErrors as $param) {
			$this->addError($param[0], $param[1]);
		}
		return $r;
	}
}
