<?php

namespace console\modules\twitter\models;

use common\api\twitter\Apps;
use console\modules\twitter\components\Tweeting;
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
    protected $_account;

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
                if($task !== null) {
                    $task['id'] = $row['id'];

                    $search = $yandex->urlInIndex($task['url']);

                    if(!$yandex->hasError()) {
                        if($search === true)
                            $this->urlInIndexSuccess($task);
                        else
                            $this->urlInIndexFail($task);
                    } else {
                        Yii::$app->redis->set('console:twitter:urlcheck:' . $row['id'], $row['id']);
                        Yii::$app->redis->expire('console:twitter:urlcheck:' . $row['id'], 5 * 60);
                        $log = "Yandex Error: " . $yandex->error . "\n";
                        echo $log;
                        Logger::error($log, [], 'daemons/tweeting/yandex', 'error');
                    }

                    $this->removeTweet($task);
                } else {
                    Yii::$app->db->createCommand()->update('{{%twitter_urlCheck}}', ['skip' => 1], ['id' => $row['id']])->execute();
                    Logger::error('json_decode return null', $row, 'daemons/tweeting/errors', 'checkIndexes-jsonDecodeError');
                }
            }
        } else {
            echo "Not tasks\n";
        }
    }

    /**
     * Если ссылка проиндексирована
     *
     * @param $row
     */
    protected function urlInIndexSuccess($row)
    {
        try {
            $t = Yii::$app->db->beginTransaction();

            Operation::unlockMoney($row['amount'], $row['amount_return'], $row['bloger_id'], $row['adv_id'], 'purse', 'indexesCheckSuccess', $row['pid'], $row['order_id']);

            /* Обновляем заказ */
            $this->updateOrder(true, $row);

            echo "Success\n";
            $t->commit();
        } catch(Exception $e) {
            echo "Success Error\n";
            Logger::error($e, $row, 'daemons/tweeting/errors', 'urlInIndexSuccess-error');
            $t->rollBack();
        }
    }

    /**
     * Если ссылка не проиндексирована
     *
     * @param $row
     */
    protected function urlInIndexFail($row)
    {
        try {
            $t = Yii::$app->db->beginTransaction();

            Operation::cancelTransfer($row['amount'], $row['bloger_id'], 'purse', 'indexesFailBloger', $row['pid']);
            Operation::returnMoney($row['amount_return'], $row['adv_id'], 'purse', 'indexesFail', $row['order_id'], $row['order_id']);

            /* Обновляем заказ */
            $this->updateOrder(false, $row);

            $t->commit();
        } catch(Exception $e) {
            echo "Fail Error\n";
            Logger::error($e, $row, 'daemons/tweeting/errors', 'urlInIndexFail-error');
            $t->rollBack();
        }
    }

    public function updateOrder($status, $row)
    {
        if($status === true)
            $status = 2;
        else
            $status = 3;

        Yii::$app->db->createCommand()->delete('{{%twitter_urlCheck}}', ['id' => $row['id']])->execute();
        Yii::$app->db->createCommand()->update('{{%twitter_ordersPerform}}', ['status' => $status], ['id' => $row['pid']])->execute();

        if(isset($row['order_hash'])) {
            $count = (new Query())
                ->from('{{%twitter_ordersPerform}}')
                ->where(['and', 'order_hash=:hash', ['or', 'status=0', 'status=1']], [':hash' => $row['order_hash']])
                ->count();

            if($count == 0)
                Yii::$app->db->createCommand()->update('{{%twitter_orders}}', ['status' => 3], ['id' => $row['id']])->execute();
        } else {
            Logger::error('unknown hash', $row, 'daemons/tweeting/errors', 'updateOrder-error');
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
                $inIds = ' AND NOT id IN(\'' . implode("', '", $ids) . '\')';
            else
                $inIds = '';

            $this->_tasks = (new Query())
                ->from('{{%twitter_urlCheck}}')
                ->where('skip=0 AND (id=7 or id=9)') //->where('skip=0 AND date_check<:date' . $inIds, [':date' => date('Y-m-d H:i:s')])
                ->all();
        }

        return empty($this->_tasks) || $this->_tasks === null ? false : $this->_tasks;
    }

    protected function removeTweet($row)
    {
        $tweeting = new Tweeting();

        $tweeting->set([
            'app_key'     => Apps::get($this->getAccount('app', $row['account_id']), '_key'),
            'app_secret'  => Apps::get($this->getAccount('app', $row['account_id']), '_secret'),
            'user_key'    => $this->getAccount('_key', $row['account_id']),
            'user_secret' => $this->getAccount('_secret', $row['account_id']),
            'ip'          => Apps::get($this->getAccount('app', $row['account_id']), 'ip'),
        ])
            ->destroy($row['tw_str_id']);

        $this->_account = null;
    }

    protected function getAccount($key, $id)
    {
        if($this->_account === null)
            $this->_account = (new Query())->from('{{%tw_accounts}}')->where(['id' => $id])->one();

        return isset($this->_account[$key]) ? $this->_account[$key] : false;
    }
}