<?php

namespace console\modules\twitter\models\orders;

trait OrdersTrait
{
	protected $_data = [];
	protected $_row = [];
	protected $_indexes = [];
	protected $_params;
	protected $_update;

	public function processOrder(array $data)
	{
		if(is_array($data) && $data !== []) {
			$this->process($data);

			print_r($this->getIndexes());
			return ['row' => $this->getRow(), 'indexes' => $this->getIndexes(), 'update' => $this->getUpdate()];
		}
		else
			return false;
	}

	public function getParams($key = NULL)
	{
		if($this->_params === NULL) {
			if(isset($this->_data['_params'])) {
				$this->_params = json_decode($this->_data['_params'], true);
			}
			else
				$this->_params = [];
		}

		if($key === NULL)
			return isset($this->_params[$key]) ? $this->_params[$key] : NULL;
		else
			return $this->_params;
	}

	protected function getUpdates()
	{
		return $this->_update;
	}

	protected function getIndexes()
	{
		return $this->_indexes;
	}

	protected function getRow()
	{
		return $this->_row;
	}

	protected function getUpdate()
	{
		return $this->_update;
	}

	public function __destruct()
	{
		$this->_data    = [];
		$this->_rows    = [];
		$this->_indexes = [];
		$this->_params  = NULL;
		$this->_update  = NULL;
	}
} 