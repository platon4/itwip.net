<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class Orders extends Model
{
	protected $_data;
	protected $_orders = [];
	protected $_order_types = [];

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
			$this->_data = (new Query)->select('*')->from('it_twitter_orders')->where(['or', 'start_date>:date', 'start_date=0000-00-00'])->limit(100)->all();

		return $this->_data;
	}

	/*
	 * Обрабатаваем полученый список заказов
	 *
	 * @return boolean
	 */
	public function generateOrders()
	{
		$orders = $this->getOrders();

		if(is_array($orders) && $orders !== array()) {
			foreach($orders as $order) {
				if(in_array($order['type_order'], $this->_order_types)) {
					$order['params'] = json_decode($order['_params']);
					$method          = new $order['type_order'];

					$this->_orders[] = $method->createOrder($order);
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