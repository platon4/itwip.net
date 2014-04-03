<?php

namespace console\modules\cleaner\controllers;

use Yii;
use yii\db\Query;

class TwitterController extends \console\components\Controller
{
    protected $run = [
        'removeOldRoster'       => 1440,
        'updatebwAccountsStats' => 1440
    ];

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
        Yii::$app->redis->set('console:cleaner:twitter:' . $method, '1', $interval * 60);
    }

    /*
     * Удаляем старые списки созданых твитов
     */
    public function removeOldRoster()
    {
        $command = Yii::$app->db->createCommand();
        $command->delete('{{%twitter_tweetsRoster}}', '_date<=:date', [':date' => date('Y-m-d', time() - 86400)])->execute();
    }

    /*
     * Обновление коло-го в черном и белом списке аккаунтов
     */
    public function updatebwAccountsStats()
    {
        $command = Yii::$app->db->createCommand();
        $query = new Query();

        $rows = $query
            ->select('id, screen_name, (SELECT COUNT(*) FROM {{%twitter_bwList}} WHERE tw_id=a.id AND _type=1) as wcount, (SELECT COUNT(*) FROM {{%twitter_bwList}} WHERE tw_id=a.id AND _type=0) as bcount')
            ->from('{{%tw_accounts}} a')
            ->all();

        $i = 0;

        foreach($rows as $row) {
            $i++;
            $command->update('{{%tw_accounts}}', ['whitelisted' => $row['wcount'], 'blacklisted' => $row['bcount']], 'id=:id', [':id' => $row['id']])->execute();
            echo $i . ". Account: " . $row['screen_name'] . "\n";
        }
    }

    public function actionClearCache()
    {
        Yii::$app->redis->delete(['twitterAccounts']);
    }
}