<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Query;
use common\helpers\String;

class DefaultController extends \console\components\Controller
{
    protected $config = [
        'patternUrl'    => "#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i",
        'tweetLength'   => 140,
        'lengthHttps'   => 23,
        'lengthHttp'    => 22,
        'hashTagsCount' => 3
    ];

    protected $urlCount = 0;
    protected $urls = [];
    protected $url;

    public function actionIndex()
    {
    }

    protected function extractUrls($tweet)
    {
        preg_match_all($this->config['patternUrl'], strtolower($tweet), $urls);

        if(!empty($urls[0])) {
            $this->urlCount = count($urls[0]);

            if($this->urlCount) {
                foreach($urls[0] as $url)
                    $this->urls[] = trim($url);
            }
        }
    }

    public function getUrl()
    {
        if($this->url === null && $this->urlCount === 1)
            $this->url = current($this->urls);

        return $this->url;
    }

    protected function migrationOrders()
    {

        $query = new Query();
        $command = Yii::$app->db->createCommand();

        $orders = $query->from('{{%tw_orders}}')->all();
        $o = 0;

        foreach($orders as $order) {
            $o++;

            try {
                $transaction = Yii::$app->db->beginTransaction();
                $order_status = $order['_status'];
                $hash = String::generateHash();

                $tasks = $query->from('{{%tweets_to_twitter}}')->where(['_order' => $order['id']])->all();

                if(!empty($tasks)) {

                    $t = 0;
                    $s = 0;

                    foreach($tasks as $task) {
                        $t++;

                        $tweet = trim($task['_tweet']);
                        $this->extractUrls($tweet);

                        $url = '';
                        $url_hash = '';

                        if($this->urlCount == 1) {
                            $url = $this->getUrl();
                            $url_hash = md5($this->getUrl());
                        }

                        $price = $task['_tweet_price'];
                        $price_return = $order['_ping'] == 1 ? $price * 0.5 : $price;

                        if(!$task['approved'])
                            $status = 0;
                        else
                            $status = strtr($task['status'], array("0" => "1", "1" => "2", "2" => "3", "3" => "4"));

                        $command->insert('{{%twitter_ordersPerform}}', [
                            'id'            => $task['id'],
                            'order_hash'    => $hash,
                            'hash'          => md5($tweet),
                            'url'           => $url,
                            'url_hash'      => $url_hash,
                            'cost'          => $price,
                            'return_amount' => $price_return,
                            'posted_date'   => $task['_placed_date'],
                            'status'        => $status,
                            '_params'       => json_encode([
                                'tweet'        => $tweet,
                                'account'      => $task['_tw_account'],
                                'tweet_str_id' => $task['str_id']
                            ]),
                        ])->execute();

                        if($order['_status'] > 0) {

                            if($task['approved'] && ($task['status'] == 1 || $task['status'] == 3)) {
                                \common\api\finance\Operation::returnMoney($price_return, $order['owner_id'], $order['_type_payment'], 4, $order['id']);
                                echo "\tReturn from tweet: " . $price_return . "\n";
                            }

                            if($task['status'] == 0)
                                $s++;
                        }

                        $this->urlCount = 0;
                        $this->urls = [];
                        $this->url = null;
                    }

                    if($order_status > 0 && $s == 0)
                        $order_status = 2;

                    $command->insert('{{%twitter_orders}}', [
                        'id'           => $order['id'],
                        'owner_id'     => $order['owner_id'],
                        'type_order'   => 'manual',
                        'order_hash'   => $hash,
                        'create_date'  => $order['_date'],
                        'status'       => $order_status,
                        'payment_type' => $order['_type_payment'],
                        '_params'      => json_encode([
                            'targeting' => [
                                'interval' => 30,
                                't'        => 'all',
                            ],
                            'ping'      => $order['_ping']
                        ])
                    ])
                        ->execute();

                }

                //$transaction->rollBack();
                $transaction->commit();
            } catch(\Exception $e) {
                $transaction->rollBack();
            }

            echo "Order: " . $o . "\n";
        }
    }
} 