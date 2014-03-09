<?php

namespace twitter\models;

use Yii;

class PlacementMethod extends \FormModel
{
	public $tid;
	public $method;
	public $filter;
	protected $methods = ['fast' => 'twitter\models\tweets\methods\Fast', 'manual' => 'twitter\models\tweets\methods\Manual'];
	protected $_model;

	public function rules()
	{
		return [
			['tid', 'ext.validators.hashValidator', 'min' => 7, 'max' => 20],
			['method', 'in_actions'],
		];
	}

	protected function afterValidate()
	{
		$attributes   = [];
		$this->_model = new $this->methods[$this->method]('method');

		if($this->method == 'manual')
			$attributes = ['filter' => $this->filter];

		$this->_model->setAttributes(array_merge(['_tid' => $this->tid], $attributes));
	}

	public function in_actions()
	{
		if(!array_key_exists($this->method, $this->methods))
			$this->addError('_type', Yii::t('twitterModule.orders', '_your_method_selected_is_invalid'));
	}

	public function getModel()
	{
		return $this->_model;
	}
}
