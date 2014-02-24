<?php

namespace console\modules\twitter\models\orders;

interface OrdersInterface
{
	public function processOrder(array $data);

	public function getAccount();

    public function getInterval();

    public function getStartDate();

	public function __destruct();
} 