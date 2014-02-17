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
		'manual' => 'console\modules\twitter\models\orders\Manual',
		'indexes' => 'console\modules\twitter\models\orders\Indexes'
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
		if($this->_data === NULL) {
			$this->_data = (new Query)
				->select('*')
				->from('it_twitter_orders')
				->where(['and', 'status=:status', ['or', 'start_date<=:date', 'start_date=0000-00-00']], [':date' => date('Y-m-d'), ':status' => 0])
				->orderBy(['id' => SORT_DESC])
				->limit(500)
				->all();
		}

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
				$this->appendOrder($order);
			}

			if(count($this->_orders))
				return true;
		}

		return false;
	}

	/*
	 * Отпровляем задания роботу на обработку
	 */
	public function putOrders()
	{
		$rows    = [];
		$indexes = [];
		$update  = [];

		foreach($this->_orders as $key => $values) {
			if(isset($values['rows']) && is_array($values['rows']) && $values['rows'] !== array()) {
				$rows['twitter:order:' . $values['rows']['id']] = json_encode($values['rows']);
			}
			else if(isset($values['indexes']) && is_array($values['indexes']) && $values['indexes'] !== array()) {
				$indexes[] = $values['indexes'];
			}
			else if(isset($values['update']) && is_array($values['update']) && $values['update'] !== array()) {
				$update[] = $values['update'];
			}
		}

		if($rows !== array()) {
			$db          = Yii::$app->db;
			$transaction = $db->beginTransaction();
			$commit      = true;

			try {
				$inserts = Yii::$app->redis
					->multi()
					->hMset('cron:twitter:orders:process', $rows);

				foreach($indexes as $index)
					$inserts->lPush($index['key'], $index['value']);

				$inserts->exec();

				foreach($inserts as $insert) {
					if($insert !== true) {
						$commit = false;
						break;
					}
				}

				if($commit === true) {
					$this->updateOrders($update);
					$transaction->commit();
				}
				else
					$transaction->rollback();

			} catch(Exception $e) {
				$transaction->rollback();
			}
		}
	}

	/*
	 * Обновляем заказы
	 */
	public function updateOrders(array $data)
	{
		if($data !== array()) {
			foreach($data as $values) {
				//Yii::$app->db->createCommand("UPDATE {{twitter_orders}} SET " . implode(", ", $values['fields']))->execute($values['params']);
			}
		}
	}

	/*
	 * Проверяем заказ, и добавляем его в список заказов для отправки роботу
	 */
	protected function appendOrder($data)
	{
		if(array_key_exists($data['type_order'], $this->_order_types)) {
			$this->_orders[] = (new $this->_order_types[$data['type_order']])->processOrder($data);
		}
	}
}