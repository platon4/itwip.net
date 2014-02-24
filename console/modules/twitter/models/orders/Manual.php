<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\base\Model;
use yii\db\Query;

class Manual implements OrdersInterface
{
	use OrdersTrait;

	/*
	 * Обработка заказа
	 */
	public  function create(array $data)
	{
        return [];
	}
}