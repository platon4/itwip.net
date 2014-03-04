<?php

namespace console\modules\twitter\controllers;

use Yii;
use console\modules\twitter\models\Orders;

class OrdersController extends \console\components\Controller
{
    /*
     * Создаем заказы для робота
     */
    public function actionCreate()
    {
        $model = new Orders();

        /* Устанавливаем сценарий валидаций */
        $model->setScenario('create');

        /* Запускаем валидатор */
        if(!$model->validate()) {
            // here put error code
        }
    }
}