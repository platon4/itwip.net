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
                    $order_id = 0;
                    $pay_type = $row['payment_method'] == 1 ? 'bonus' : 'purse';

                    try {
                        $t = Yii::$app->db->beginTransaction();

                        if($status === 0) {
                            Operation::unlockMoney($row['tweet_cost'], $row['return_amount'], $row['bloger_id'], $row['adv_id'], $pay_type, 'tweetsCheckSuccess', $row['tweet_id'], $order_id);

                            $command->update('{{%twitter_tweets}}', ['status' => 1], ['id' => $row['id']])->execute();

                            echo 'Success' . PHP_EOL;
                        } else {
                            Operation::cancelTransfer($row['tweet_cost'], $row['bloger_id'], $pay_type, 'tweetCheckUnsuccessfully', $row['tweet_id'], $row['screen_name']);
                            Operation::returnMoney($row['return_amount'], $row['adv_id'], $pay_type, 'bloggerDeletedTweet', $order_id, $order_id);

                            $command->update('{{%twitter_tweets}}', ['status' => 2], ['id' => $row['id']])->execute();

                            if($status === 1)
                                (new Accounts())->isSuspended($row['tw_account']);

                            echo 'Cancel' . PHP_EOL;
                        }

                        print_r($row);
                        $t->commit();
                    } catch(Exception $e) {
                        $t->rollBack();
                    }
                }
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
                ->limit(1)
                ->all();
        }

        return $this->_tasks;
    }
}