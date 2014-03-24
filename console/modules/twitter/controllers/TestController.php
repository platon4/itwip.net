<?php

namespace console\modules\twitter\controllers;

use common\helpers\String;
use Yii;
use console\components\Controller;
use yii\base\Exception;
use yii\db\Query;

class TestController extends Controller
{
    public function actionIndex()
    {
        $query = new Query();

        $rows = $query->from('{{%tw_orders}} o')->where(
            ['exists', (new Query())->select('id')->from('{{%twitter_orders}} no')->where('no.id=o.id')]
        )
            //->limit(1)
            ->all();

        foreach($rows as $row) {
            $hash = String::generateHash();
            $status = $row['_status'];
            $payment_type = $row['_type_payment'];

            try {
                $t = Yii::$app->db->beginTransaction();

                $tasks = (new Query())->from('{{%tweets_to_twitter}}')->where(['_order' => $row['id']])->all();

                $inserts = [];
                $tc = 0;
                foreach($tasks as $task) {

                    $norder = (new Query())->from('{{%twitter_orders}}')->where(['id' => $row['id']])->one();
                    $order_hash = $norder['order_hash'];

                    if(!$task['approved'])
                        $task_status = 0;
                    else
                        $task_status = strtr($task['status'], [0 => 1, 1 => 2, 2 => 3, 3 => 4]);

                    $url = $this->extractUrls($task['_tweet']);

                    $tweet_hash = md5($task['_tweet']);
                    $params = json_encode([
                        'tweet'    => $task['_tweet'],
                        'account'  => $task['_tw_account'],
                        'tweet_id' => $task['str_id']
                    ]);

                    $price = $task['_tweet_price'];

                    $inserts[] = [
                        $task['id'],
                        $hash,
                        $tweet_hash,
                        $url,
                        md5($url),
                        $price,
                        ($row['_ping'] == 1 ? $price + 0.50 : $price),
                        $task_status,
                        $params
                    ];

                    if($row['_status'] != 0 && ($task_status == 0 || $task_status == 1))
                        $tc++;
                }

                if($tc > 0)
                    $status = 1;
                elseif($row['_status'] != 0 && $tc === 0)
                    $status = 2;

                //print_r((new Query())->from('{{%tweets_to_twitter}}')->where(['_order' => $row['id']])->one());
                //print_r((new Query())->from('{{%twitter_ordersPerform}}')->where(['order_hash' => $order_hash])->one());

                Yii::$app->db->createCommand()->delete('{{%twitter_ordersPerform}}', ['order_hash' => $order_hash])->execute();
                Yii::$app->db->createCommand()->batchInsert('{{%twitter_ordersPerform}}', ['id', 'order_hash', 'hash', 'url', 'url_hash', 'cost', 'return_amount', 'status', '_params'], $inserts)->execute();

                Yii::$app->db->createCommand()->update('{{%twitter_orders}}', [
                    'owner_id'     => $row['owner_id'],
                    'type_order'   => 'manual',
                    'order_hash'   => $hash,
                    'create_date'  => $row['_date'],
                    'status'       => $status,
                    'payment_type' => $payment_type,
                    '_params'      => '{"targeting":{"interval":30,"t":"all"},"ping":"0"}'
                ], ['id' => $row['id']])->execute();

                $t->commit();
            } catch(Exception $e) {
                echo $e;
                $t->rollBack();
            }
        }
    }

    public function processOrders()
    {
        $command = Yii::$app->db->createCommand();
        $orders = (new Query())->from('{{%twitter_orders}}')->where(['type_order' => 'indexes'])->all();

        foreach($orders as $order) {
            $command->update('{{%twitter_orders}}', ['status' => 1, 'is_process' => 0], ['id' => $order['id']])->execute();
            $command->update('{{%twitter_ordersPerform}}', ['status' => 0, 'is_process' => 0], ['order_hash' => $order['order_hash']])->execute();
        }
    }

    protected
    function extractUrls($tweet)
    {
        preg_match_all("#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i", strtolower($tweet), $urls);

        if(!empty($urls[0])) {
            $count = count($urls[0]);

            if($count)
                foreach($urls[0] as $url)
                    return trim($url);
        }
    }

} 