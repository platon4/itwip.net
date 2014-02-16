<?php

namespace console\modules\twitter\models\orders;

abstract class Orders
{
	public $_order = [];

	public function processOrder($data)
	{
		return $this->_order;
	}

	public function updateOrders()
	{
		
	}
} 