<?php

namespace console\modules\twitter\models\orders;

interface OrdersInterface
{
	public function processOrder(array $data);

	public function getAccount();

	public function __destruct();
} 