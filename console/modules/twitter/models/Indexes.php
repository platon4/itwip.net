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
        if($this->getTasks() !== false) {

            $yandex = new Yandex(Yii::$app->params['yandex']['user'], Yii::$app->params['yandex']['key']);

            foreach($this->getTasks() as $row) {
                $task = json_decode($row['_params'], true);
                $task['id'] = $row['id'];

                $search = $yandex->urlInIndex($task['url']);

                if(!$yandex->hasErrors()) {
                    if($search === true)
                        $this->urlInIndexSuccess($task);
                    else
                        $this->urlInIndexFail($task);
                } else {
                    Yii::$app->redis->set('console:twitter:urlcheck:' . $row['id'], $row['id']);
                    Yii::$app->redis->expire('console:twitter:urlcheck:' . $row['id'], 5 * 60);
                    $log = "Yandex Error: " . $yandex->error . "\n";
                    echo $log;
                    Logger::log($log, 2);
                }

                $this->removeTweet($task);
            }
        } else {
            echo "Not tasks\n";
        }
    }

    protected function urlInIndexSuccess($row)
    {
        try {
            $t = Yii::$app->db->beginTransaction();

            Operation::unlockMoney($row['amount'], $row['return_amount'], $row['bloger_id'], $row['adv_id'], 'purse', 'indexesCheck', $row['pid'], $row['order_id']);

            /* Обновляем заказ */
            $this->updateOrder(true, $row);

            echo "Success\n";
            $t->commit();
        } catch(Exception $e) {
            echo "Success Error\n";
            $t->rollBack();
        }
    }

    protected function urlInIndexFail($row)
    {
        try {
            $t = Yii::$app->db->beginTransaction();

            Operation::cancelTransfer($row['amount'], $row['bloger_id'], 'purse', 'indexesCheck', $row['pid']);
            Operation::returnMoney($row['amount_return'], $row['adv_id'], 'purse', 'indexesCheck', $row['pid'], $row['order_id']);

            /* Обновляем заказ */
            $this->updateOrder(false, $row);

            $t->commit();
        } catch(Exception $e) {
            $t->rollBack();
        }

        echo "Fail\n";
    }

    public function updateOrder($status, $row)
    {
        if($status === true)
            $status = 2;
        else
            $status = 3;

        Yii::$app->db->createCommand()->update('{{%twitter_ordersPerform}}', ['status' => $status], ['id' => $row['pid']]);
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
                $inIds = ' AND NOT id IN(\'' . implode("', '", $ids) . '\')';
            else
                $inIds = '';

            $this->_tasks = (new Query())
                ->from('{{%twitter_urlCheck}}')
                ->where('date_check<:date' . $inIds, [':date' => date('Y-m-d H:i:s')])
                ->all();
        }

        return empty($this->_tasks) || $this->_tasks === null ? false : $this->_tasks;
    }

    protected function removeTweet($row)
    {

    }
}