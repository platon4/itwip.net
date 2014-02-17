<?php

namespace console\modules\twitter\models\orders;

class Indexes implements  OrdersInterface
{
	use OrdersTrait;

	protected function process($data)
	{
		print_r($data);
		die();
	}

	/*
	 * Освобождаем память
	 */

}