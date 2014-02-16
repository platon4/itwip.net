<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class Orders extends Model
{
	protected $_data;
	protected $_orders = [];
	protected $_order_types = [
		'manual' => 'console\modules\twitter\models\orders\Indexes',
		'indexes' => 'console\modules\twitter\models\orders\Manual'
	];

	public function rules()
	{
		return [
			['order', 'orderFound', 'skipOnEmpty' => false, 'on' => 'create']
		];
	}

	/*
	 * Проверяем если есть заказы в ожидание
	 */
	public function orderFound()
	{
		if($this->getOrders() === false)
			$this->addError('order', 'Not orders found to process.');
	}

	/*
	 * Берем 100 заказов для оброботки
	 *
	 * @return array
	 */
	public function getOrders()
	{
		if($this->_data === NULL)
			$this->_data = (new Query)
				->select('*')
				->from('it_twitter_orders')
				->where(['or', 'start_date>:date', 'start_date=0000-00-00'], [':date' => date('Y-m-d')])
				->limit(500)
				->all();

		return $this->_data;
	}

	/*
	 * Обрабатаваем полученый список заказов
	 *
	 * @return boolean
	 */
	public function processOrders()
	{
		$orders = $this->getOrders();

		if(is_array($orders) && $orders !== array()) {
			foreach($orders as $order) {
				if(array_key_exists($order['type_order'], $this->_order_types)) {
					$this->_orders[] = (new $this->_order_types[$order['type_order']])->processOrder(array_merge($order, ['params' => json_decode($order['_params'])]));
				}
			}

			if(count($this->_orders))
				return true;
		}

		return false;
	}

	public function putOrders()
	{

	}
} 