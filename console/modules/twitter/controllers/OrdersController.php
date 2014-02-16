<?php

namespace console\modules\twitter\controllers;

use Yii;
use console\modules\twitter\models\Orders;

class OrdersController extends \console\components\Controller
{
	/*
	 * Создаем заказы для робота
	 */
	public function actionCreateOrders()
	{
		$model = new Orders;
		/* Устанавливаем сценарий валидаций */
		$model->setScenario('create');

		/* Запускаем валидатор */
		if($model->validate()) {
			/* Генерируем заказы, если заказы есть,то посылаем их роботу */
			if($model->generateOrders()) {
				/* Отпровляем сгенерированные заказы роботу */
				$model->putOrders();
			}
		}
	}
}