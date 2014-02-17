<?php

namespace console\modules\twitter\models\orders;

interface OrdersInterface
{
	public function processOrder(array $data);

	public function getParams($key = NULL);

	public function __destruct();
} 