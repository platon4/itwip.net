<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use console\components\Logger;
use console\modules\twitter\components\Yandex;
use common\api\finance\Operation;

class Indexes extends Model
{
    protected $_tasks;

    public function rules()
    {
        return [
            ['run', 'checkIndexes', 'skipOnEmpty' => false, 'on' => 'check']
        ];
    }

    public function checkIndexes()
    {
        /*
         * Добавление на баланс пользователя (в заблакированые)
         *
         * Сумма, Ид пользователя, типа валюты, типа операций, Ид заказа
         */
        //Operation::put(1, 1, 'purse', 'indexesCheck', 1);
        //Operation::unlockMoney(1, 1, 'purse', 'indexesCheck', 1);
        /* Разблокирование средств пользователю за удачное проверку */
        //Operation::unlockMoney(1, 1, 0, 1, 6);
        die();
        if($this->getTasks() !== false) {

            $yandex = new Yandex(Yii::$app->params['yandex']['user'], Yii::$app->params['yandex']['key']);

            foreach($this->getTasks() as $task) {
                $search = $yandex->urlInIndex($task['url']);

                if(!$yandex->hasErrors()) {
                    if($search === true)
                        $this->urlInIndexSuccess($task);
                    else
                        $this->urlInIndexFail($task);
                } else {
                    //Yii::$app->redis->set('console:twitter:urlcheck:' . $task['id'], $task['id']);
                    //Yii::$app->redis->expire('console:twitter:urlcheck:' . $task['id'], 5 * 60);
                    $log = "Yandex Error: " . $yandex->error . "\n";
                    echo $log;
                    Logger::log($log, 2);
                }
            }
        } else {
            echo "Not tasks\n";
        }
    }

    protected function urlInIndexSuccess($row)
    {
        $order = (new Query)->from('{{%twitter_orders}}')->where(['order_hash' => $row['order_hash']])->one();

        if($order !== false) {
            try {
                $t = Yii::$app->db->beginTransaction();

                Operation::unlockMoney($row['cost'], 1, ($order['payment_type'] == 1 ? 'bonus' : 'purse'), 'indexesCheck', $row['pid']);

                /* Обновляем заказ */
                $this->updateOrder(true, $row);

                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }

            echo "Success\n";
        }
    }

    protected function urlInIndexFail($row)
    {
        $order = (new Query)->from('{{%twitter_orders}}')->where(['order_hash' => $row['order_hash']])->one();

        if($order !== false) {
            try {
                $t = Yii::$app->db->beginTransaction();

                //Operation::unlockMoney($row['cost'], 1, ($order['payment_type'] == 1 ? 'bonus' : 'purse'), 'indexesCheck', $row['pid']);

                /* Обновляем заказ */
                //$this->updateOrder(false, $row);

                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }

            echo "Fail\n";
        }
    }

    public function updateOrder($status, $row)
    {
        $command = Yii::$app->db->createCommand();

        if($status === true)
            $status = 2;
        else
            $status = 3;

        $command->update('{{%twitter_ordersPerform}}', ['status' => $status], ['id' => $row['pid']]);
    }

    public function getTasks()
    {
        if($this->_tasks === null) {
            $rids = Yii::$app->redis->mGet(Yii::$app->redis->keys('console:twitter:urlcheck:*'));

            $ids = [];

            if(!empty($rids)) {
                foreach($rids as $id) {
                    if($id !== false)
                        $ids[] = $id;
                }
            }

            if(!empty($ids))
                $inIds = ' AND NOT u.id IN(\'' . implode("', '", $ids) . '\')';
            else
                $inIds = '';

            $this->_tasks = (new Query())->select('u.id, p.id as pid, p.order_hash, p.url, p.cost, p.return_amount')->from('{{%twitter_urlCheck}} u')->innerJoin('{{%twitter_ordersPerform}} p', 'u.orderPerform_id=p.id')->where('u.date_check<:date' . $inIds, [':date' => date('Y-m-d H:i:s')])->all();
        }

        return empty($this->_tasks) || $this->_tasks === null ? false : $this->_tasks;
    }
}