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
        $tweets = (new Query())->from('{{%tw_tweets}}')->orderBy(['id' => SORT_DESC])->all();

        $i = 0;
        foreach($tweets as $tweet) {
            try {
                $i++;
                $t = Yii::$app->db->beginTransaction();

                $order_id = (new Query())->select('_order')->from('{{%tweets_to_twitter}}')->where(['str_id' => $tweet['tw_id']])->scalar();
                $hash = '';

                if(trim($order_id) != '') {
                    $hash = (new Query())->select('order_hash')->from('{{%twitter_orders}}')->where(['id' => $order_id])->scalar();
                }

                $insert = [
                    'id'             => $tweet['id'],
                    'order_hash'     => $hash,
                    'order_type'     => 'manual',
                    'adv_id'         => $tweet['ot_id'],
                    'bloger_id'      => $tweet['owner_id'],
                    'tweet_hash'     => md5($tweet['_text']),
                    'tweet'          => $tweet['_text'],
                    'tweet_id'       => $tweet['tw_id'],
                    'tw_account'     => $tweet['tid'],
                    'date'           => $tweet['_date'],
                    'tweet_cost'     => $tweet['_cost'],
                    'return_amount'  => $tweet['_cost'],
                    'payment_method' => $tweet['pay_type'],
                    'status'         => $tweet['_status']
                ];

                Yii::$app->db->createCommand()->insert('{{%twitter_tweets}}', $insert)->execute();

                echo "Process: " . $i . PHP_EOL;
                $t->commit();
            } catch(Exception $e) {
                echo $e;
                $t->rollBack();
            }
        }
    }

    public function actionProcessOrders()
    {
        $command = Yii::$app->db->createCommand();
        $orders = (new Query())->from('{{%twitter_orders}}')->where(['type_order' => 'indexes'])->all();

        foreach($orders as $order) {
            $command->update('{{%twitter_orders}}', ['status' => 1, 'is_process' => 0], ['id' => $order['id']])->execute();
            $command->update('{{%twitter_ordersPerform}}', ['status' => 0, 'is_process' => 0], ['order_hash' => $order['order_hash']])->execute();
        }
    }

    protected function extractUrls($tweet)
    {
        preg_match_all("#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i", strtolower($tweet), $urls);

        if(!empty($urls[0])) {
            $count = count($urls[0]);

            if($count)
                foreach($urls[0] as $url)
                    return trim($url);
        }
    }

    public function actionOrderMigration()
    {
        $rows = (new Query())->from('{{%tw_orders}} o')->where(
            ['exists', (new Query())->select('id')->from('{{%twitter_orders}} no')->where('no.id=o.id')]
        )
            //->limit(1)
            ->all();

        foreach($rows as $row) {
            echo 'Order process: ' . $row['id'] . PHP_EOL;
            $hash = String::generateHash();
            $status = $row['_status'];
            $payment_type = $row['_type_payment'];

            try {
                $t = Yii::$app->db->beginTransaction();

                $tasks = (new Query())->from('{{%tweets_to_twitter}}')->where(['_order' => $row['id']])->all();

                $inserts = [];
                $tc = 0;
                foreach($tasks as $task) {
                    echo "\t\tTask process: " . $row['id'] . PHP_EOL;
                    $norder = (new Query())->from('{{%twitter_orders}}')->where(['id' => $row['id']])->one();
                    $order_hash = $norder['order_hash'];

                    if(!$task['approved'])
                        $task_status = 0;
                    else
                        $task_status = strtr($task['status'], [0 => 1, 1 => 2, 2 => 3, 3 => 4]);

                    $url = $this->extractUrls($task['_tweet']);

                    $tweet_hash = md5($task['_tweet']);

                    $price = $task['_tweet_price'];
                    $post_date = $task['_placed_date'];

                    $bloger_id = (new Query())->select('owner_id')->from('{{%tw_accounts}}')->where(['id' => $task['_tw_account']])->scalar();

                    $inserts[] = [
                        $task['id'],
                        $hash,
                        $tweet_hash,
                        $url,
                        md5($url),
                        $price,
                        ($row['_ping'] == 1 ? $price + 0.50 : $price),
                        $post_date,
                        $task_status,
                        $task['_tweet'],
                        $bloger_id,
                        $task['_tw_account'],
                        $task['str_id']
                    ];

                    if($row['_status'] != 0 && ($task_status == 0 || $task_status == 1))
                        $tc++;
                }

                if($tc > 0)
                    $status = 1;
                elseif($row['_status'] != 0 && $tc === 0)
                    $status = 2;

                Yii::$app->db->createCommand()->delete('{{%twitter_ordersPerform}}', ['order_hash' => $order_hash])->execute();
                Yii::$app->db->createCommand()->batchInsert('{{%twitter_ordersPerform}}', [
                    'id',
                    'order_hash',
                    'hash',
                    'url',
                    'url_hash',
                    'cost',
                    'return_amount',
                    'posted_date',
                    'status',
                    'tweet',
                    'bloger_id',
                    'tw_account',
                    'tweet_id'
                ], $inserts)->execute();

                Yii::$app->db->createCommand()->update('{{%twitter_orders}}', [
                    'owner_id'     => $row['owner_id'],
                    'type_order'   => 'manual',
                    'order_hash'   => $hash,
                    'create_date'  => $row['_date'],
                    'status'       => (string) $status,
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

    public function updateTasks()
    {

        $ordersNew = (new Query())->from('{{%twitter_ordersPerform}}')
            ->orderBy(['id' => SORT_DESC])
            ->all();

        foreach($ordersNew as $zla) {
            echo "Task update: " . $zla['id'] . PHP_EOL;
            $params = json_decode($zla['_params'], true);

            if(!empty($params)) {

                if(isset($params['tweet'])) {
                    $tweet = $params['tweet'];
                    $bloger_id = isset($params['account']) ? (new Query())->select('owner_id')->from('{{%tw_accounts}}')->where(['id' => $params['account']])->scalar() : 0;
                    $tw_account = isset($params['account']) ? $params['account'] : 0;

                    if(isset($params['tweet']))
                        unset($params['tweet']);

                    if(isset($params['account']))
                        unset($params['account']);

                    $par = !empty($params) ? json_encode($params) : '';

                    Yii::$app->db->createCommand()
                        ->update('{{%twitter_ordersPerform}}', ['tweet' => $tweet, 'bloger_id' => $bloger_id, 'tw_account' => $tw_account, '_params' => $par], ['id' => $zla['id']])
                        ->execute();
                } else {
                    echo "undefinited index: tweet: " . $zla['id'] . PHP_EOL;
                }
            }
        }
    }
} 