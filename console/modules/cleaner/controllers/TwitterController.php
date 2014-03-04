<?php

namespace console\modules\cleaner\controllers;

use Yii;
use yii\db\Query;

class TwitterController extends \console\components\Controller
{
    protected $command;
    protected $query;

    protected $run = [
        'removeoldroster'       => 1440,
        'updatebwAccountsStats' => 1440
    ];

    public function init()
    {
        $this->command = Yii::$app->db->createCommand();
        $this->query = new Query();
    }

    public function actionIndex()
    {
        foreach($this->run as $method => $interval) {
            if($this->checkInterval($method) === true) {
                $this->setInterval($method, $interval);
                $this->$method($interval, $method);
            }
        }
    }

    public function checkInterval($method)
    {
        if(Yii::$app->redis->exists('console:cleaner:twitter:' . $method) === true)
            return false;

        return true;
    }

    public function setInterval($method, $interval)
    {
        Yii::$app->redis->set('console:cleaner:twitter:' . $method, true);
        Yii::$app->redis->expire('console:cleaner:twitter:' . $method, $interval * 60);
    }

    /*
     * Удаляем старые списки созданых твитов
     */
    public function removeoldroster()
    {
        $this->command->delete('{{%twitter_tweetsRoster}}', '_date<=:date', [':date' => date('Y-m-d', time() - 86400)])->execute();
    }

    /*
     * Обновление коло-го в черном и белом списке аккаунтов
     */
    public function updatebwAccountsStats()
    {
        $rows = $this->query->select('id, screen_name, (SELECT COUNT(*) FROM {{%twitter_bwList}} WHERE tw_id=a.id AND _type=1) as wcount, (SELECT COUNT(*) FROM {{%twitter_bwList}} WHERE tw_id=a.id AND _type=0) as bcount')->from('{{%tw_accounts}} a')->all();
        $i = 0;

        foreach($rows as $row) {
            $i++;
            $this->command->update('{{%tw_accounts}}', ['whitelisted' => $row['wcount'], 'blacklisted' => $row['bcount']], 'id=:id', [':id' => $row['id']])->execute();
            echo $i . ". Account: " . $row['screen_name'] . "\n";
        }
    }
}