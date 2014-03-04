<?php

namespace twitter\models\orders;

use Yii;
use twitter\models\orders\status\Orders;

class Status extends \FormModel
{
	public $t;
	public $m;
	protected $types = [
		'manual' => 'Размещение твитов (ручной выбор аккаунтов)',
		'indexes' => 'Быстрая индексация ссылок (время индексаций {time})',
	];

	protected $_order;
	protected $actions = [
		'manual' => ['class' => 'twitter\models\orders\status\Manual', 'index' => '_mDetails', 'rows' => '_mDetailsRows'],
		'indexes' => ['class' => 'twitter\models\orders\status\Indexes', 'index' => '_fDetails', 'rows' => '_fDetailsRows'],
	];

	public function rules()
	{
		return [
			['t', 'in', 'range' => array_keys($this->actions)],
		];
	}

	public function attributeLabels()
	{
		return [
			't' => 'Тип заказа',
		];
	}

	public function afterValidate()
	{
		if($this->t !== NULL) {
			$class   = $this->actions[$this->t]['class'];
			$this->m = new $class;
		}
		else
			$this->m = new Orders;

		$this->m->setAttributes($this->getAttributes(NULL, true));

		if(!$this->m->validate())
			$this->addError('order', $this->m->getError());
	}

	public function getOrderType($type)
	{
		return isset($this->types[$type]) ? $this->types[$type] : 'Тип заказа не определен';
	}

	public function getViewFile()
	{
		return $this->m->getView();
	}
}
