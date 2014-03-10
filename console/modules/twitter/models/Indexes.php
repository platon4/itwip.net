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

                Operation::put($row['cost'], $order['owner_id'], $order['payment_type'], 6);

                $this->updateOrder(true);
                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }

            echo "Success\n";
        }
    }

    protected function urlInIndexFail($row)
    {
        echo 'fail';
    }

    public function updateOrder($status)
    {
        if($status === true) {

        } else {

        }
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

            $this->_tasks = (new Query())->select('u.id, p.order_hash, p.url, p.cost, p.return_amount')->from('{{%twitter_urlCheck}} u')->innerJoin('{{%twitter_ordersPerform}} p', 'u.orderPerform_id=p.id')->where('u.date_check<:date' . $inIds, [':date' => date('Y-m-d H:i:s')])->all();
        }

        return empty($this->_tasks) || $this->_tasks === null ? false : $this->_tasks;
    }
}