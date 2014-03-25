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
            foreach($this->getTasks() as $row) {
                print_r($row);
            }
        } else {
            echo "Not tasks\n";
        }
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