<?php

namespace console\modules\twitter\models\orders;

trait OrdersTrait
{
	protected $_data = [];
	protected $_tasks = [];
	protected $_indexes = [];
	protected $_params;
	protected $_update;

	/*
	 * Обрабатываем заказ
	 */
	public function processOrder(array $order)
	{
		if(is_array($order) && $order !== []) {
			$this->process($order);
			return ['tasks' => $this->getTasks(), 'indexes' => $this->getIndexes(), 'update' => $this->getUpdate()];
		}
		else
			return false;
	}

	/*
	 * Получаем пареметры заказа
	 *
	 * @return array or @return null
	 */
	public function getTaskParams($key = NULL)
	{
		if($this->_params === NULL) {
			if(isset($this->_data['_params'])) {
				$this->_params = json_decode($this->_data['_params'], true);
			}
			else
				$this->_params = [];
		}

		if($key === NULL)
			return $this->_params;
		else
			return isset($this->_params[$key]) ? $this->_params[$key] : NULL;
	}

	/*
	 * Возворощяем поля для обновление азказа
	 */
	protected function getUpdates()
	{
		return $this->_update;
	}

	protected function getIndexes()
	{
		return $this->_indexes;
	}

	protected function getTasks()
	{
		return $this->_tasks;
	}

	protected function getUpdate()
	{
		return $this->_update;
	}

	/*
	 * Очищаем память
	 */
	public function __destruct()
	{
		$this->_data    = [];
		$this->_tasks    = [];
		$this->_indexes = [];
		$this->_params  = NULL;
		$this->_update  = NULL;
	}
} 