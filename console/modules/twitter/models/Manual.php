<?php

namespace console\modules\twitter\models;

use common\api\twitter\Accounts;
use common\helpers\Url;
use console\modules\twitter\components\Tweeting;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use console\components\Logger;
use common\api\finance\Operation;

class Manual extends Model
{
    protected $_tasks;

    public function rules()
    {
        return [
            ['run', 'checkTweets', 'skipOnEmpty' => false, 'on' => 'check']
        ];
    }

    public function checkTweets()
    {
        if($this->getTasks() !== false) {
            $command = Yii::$app->db->createCommand();

            foreach($this->getTasks() as $row) {
                $status = $this->tweetStatus($row['tweet_id']);

                if($status != 3) {
                    echo "Order hash: " . $row['order_hash'] . PHP_EOL;

                    $order_id = trim($row['order_hash']) != '' ? (new Query())->select('id')->from('{{%twitter_orders}}')->where(['order_hash' => $row['order_hash']])->scalar() : 0;

                    echo $order_id . PHP_EOL;

                    $pay_type = $row['payment_method'] == 1 ? 'bonus' : 'purse';

                    if(empty($row['screen_name']))
                        $row['screen_name'] = 'Account was deleted from the system';

                    try {
                        $t = Yii::$app->db->beginTransaction();

                        if($status === 0) {
                            Operation::unlockMoney($row['tweet_cost'], $row['return_amount'], $row['bloger_id'], $row['adv_id'], $pay_type, 'tweetsCheckSuccess', $row['tweet_id'], $order_id, $row['screen_name']);

                            $command->update('{{%twitter_tweets}}', ['status' => '1'], ['id' => $row['id']])->execute();

                            echo 'Success' . PHP_EOL;
                            Logger::log($row, 'check-tweet', 'success-check-tweet');
                        } else {
                            Operation::cancelTransfer($row['tweet_cost'], $row['bloger_id'], $pay_type, 'tweetCheckUnsuccessfully', $row['tweet_id'], $row['screen_name']);
                            Operation::returnMoney($row['return_amount'], $row['adv_id'], $pay_type, 'bloggerDeletedTweet', $order_id, $order_id);

                            $command->update('{{%twitter_tweets}}', ['status' => '2'], ['id' => $row['id']])->execute();

                            if($status === 1)
                                (new Accounts())->isSuspended($row['tw_account']);

                            echo 'Cancel' . PHP_EOL;
                            Logger::log($row, 'check-tweet', 'error-check-tweet');
                        }

                        $t->commit();
                    } catch(Exception $e) {
                        Logger::error($e, $row, 'check-tweet', 'exception-check-tweet');
                        $t->rollBack();
                    }
                }

                $sec = rand(1, 5);
                echo 'Timeout ' . $sec . PHP_EOL;
                sleep($sec);
            }
        } else {
            echo "Not tasks\n";
        }
    }

    protected function tweetStatus($str_id)
    {
        $request = Url::get('https://twitter.com/statuses/' . $str_id, [], 'GET', true);

        if(isset($request['info']['url']) && strpos($request['info']['url'], 'account/suspended') !== false) {
            return 1;
        } else if($request['code'] == 200) {
            return 0;
        } else if($request['code'] == 404)
            return 2;
        else
            return 3;
    }

    protected function getTasks()
    {
        if($this->_tasks === null) {
            $this->_tasks = (new Query())
                ->select('t.*,a.screen_name')
                ->from('{{%twitter_tweets}} t')
                ->leftJoin('{{%tw_accounts}} a', 't.tw_account=a.id')
                ->where(['and', 't.status=0', 't.date<:date'], [':date' => date("Y-m-d H:i:s", time() - (3 * 86400))])
                ->orderBy(['t.date' => SORT_ASC])
                ->limit(10)
                ->all();
        }

        return $this->_tasks;
    }
}