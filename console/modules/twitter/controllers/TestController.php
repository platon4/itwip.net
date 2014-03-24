<?php

namespace console\modules\twitter\controllers;

use Yii;
use console\components\Controller;
use yii\db\Query;

class TestController extends Controller
{
    public function actionIndex()
    {
        die();
        $command = Yii::$app->db->createCommand();

        $orders = (new Query())->from('{{%twitter_orders}}')->where(['type_order' => 'indexes'])->all();

        foreach($orders as $order) {
            $command->update('{{%twitter_orders}}', ['status' => 1, 'is_process' => 0], ['id' => $order['id']])->execute();
            $command->update('{{%twitter_ordersPerform}}', ['status' => 0, 'is_process' => 0], ['order_hash' => $order['order_hash']])->execute();
        }
    }
} 