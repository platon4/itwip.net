<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Query;

class RunController extends \console\components\Controller
{
    protected $c;
    protected $q;
    protected $b;

    public function init()
    {
        $this->c = Yii::$app->db->createCommand();
        $this->q = new Query;
    }

    public function actionIndex()
    {
        echo $this->c->update('{{%tw_accounts_settings}}', ['_timeout' => rand(15, 30)], '_timeout<15')->execute();
    }

    /*
     * Обновление коло-го в черном и белом списке аккаунтов
     */
    public function updateBwAccountsStats()
    {
        $rows = $this->q->select('id, screen_name, (SELECT COUNT(*) FROM {{%twitter_bwList}} WHERE tw_id=a.id AND _type=1) as wcount, (SELECT COUNT(*) FROM {{%twitter_bwList}} WHERE tw_id=a.id AND _type=0) as bcount')->from('{{%tw_accounts}} a')->all();
        $i = 0;

        foreach($rows as $row) {
            $i++;
            $this->c->update('{{%tw_accounts}}', ['whitelisted' => $row['wcount'], 'blacklisted' => $row['bcount']], 'id=:id', [':id' => $row['id']])->execute();
            echo $i . ". Account: " . $row['screen_name'] . "\n";
        }
    }
}