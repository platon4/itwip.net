<?php

namespace twitter\models\tweets;

use Yii;

class Finality extends \FormModel
{
	public $_tid;
	protected $count;

	public function rules()
	{
		return [
			['_tid', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15],
			['_tid', 'tweetsValid']
		];
	}

	public function getCount()
	{
		if($this->count === NULL)
			$this->count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE _key=:_key AND _placement=1")->queryScalar(array(':_key' => $this->_tid));

		return $this->count;
	}

	public function getFilters()
	{
		return [];
	}

	public function tweetsValid()
	{
		if(!$this->getCount())
			$this->addError('_tid', 'Твиты подходящие для размещение не найдены, или заказ уже создан.');
	}
}
