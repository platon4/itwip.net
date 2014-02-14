<?php

namespace twitter\models\orders\status;

use Yii;

class Manual extends \FormModel
{
	public $h;
	public $limit;
	protected $_pages;
	protected $_rows;
	protected $_order;
	protected $_count;
	protected $limits = [
		10 => ['value' => 10, 'title' => 10],
		20 => ['value' => 20, 'title' => 20],
		30 => ['value' => 30, 'title' => 30],
		40 => ['value' => 40, 'title' => 40],
		50 => ['value' => 50, 'title' => 50]
	];

	public function rules()
	{
		return [
			['h', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15],
			['limit', 'in', 'range' => array_keys($this->limits), 'message' => 'Неправильное количество заказов на странице.'],
			['_count', 'getCount']
		];
	}

	public function afterValidate()
	{
		if($this->getOrder() === false)
			$this->addError('order', 'Извините, но такого заказа нету.');
	}

	public function getCount()
	{
		if($this->_count === NULL) {
			$this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_orders_perform}} WHERE order_hash=:hash")->queryScalar([':hash' => $this->h]);
		}

		if($this->_count <= 0)
			$this->addError('order', 'К сожалению, мы не смогли найти запрашиваемый вами заказ.');

		return $this->_count;
	}

	public function getRows()
	{
		if($this->_rows === NULL) {
			$orders = Yii::app()->db->createCommand("SELECT id,cost,return_amount,status,_params,message FROM {{twitter_orders_perform}} WHERE id=:id")->queryAll(true, [':hash' => $this->h]);
			$rows   = [];
			$ids    = [];

			foreach($orders as $order) {
				$order['params'] = json_decode($orser['_params'], true);
				unset($order['_params']);

				$ids[]  = $order['params']['account'];
				$rows[] = $order;
			}

			$this->_rows = $rows;
		}

		return $this->_rows;
	}

	public function getOrder()
	{
		if($this->id) {
			if($this->_order === NULL) {
				$this->_order = Yii::app()->db->createCommand("SELECT * FROM {{twitter_orders}} WHERE id=:id AND owner_id=:owner AND type_order='manual'")->queryRow(true, [':owner' => Yii::app()->user->id, ':id' => $this->id]);
			}

			return $this->_order;
		}
		else
			throw new \CHttpException('502', Yii::t('yii', 'Error get order, because Order ID not set.'));
	}

	public function getPages()
	{
		if($this->_pages === NULL) {
			$this->_pages           = new \CPagination(0);
			$this->_pages->pageSize = 10;
		}
	}

	public function getView()
	{
		if(Yii::app()->request->isAjaxRequest)
			return 'status/_mDetailsRows';
		else
			return 'status/_mDetails';
	}
}