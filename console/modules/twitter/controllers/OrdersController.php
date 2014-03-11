<?php

namespace console\modules\twitter\controllers;

use Yii;
use console\modules\twitter\models\Orders;
use console\modules\twitter\models\Indexes;
use yii\db\Query;

// */2 * * * * php /var/www/itwip/cmd twitter/orders/create
// */5 * * * * php /var/www/itwip/cmd twitter/orders/check-indexes

class OrdersController extends \console\components\Controller
{
    /**
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

    public function actionCheckIndexes()
    {
        $model = new Indexes();
        $model->setScenario('check');

        $model->validate();
    }
}