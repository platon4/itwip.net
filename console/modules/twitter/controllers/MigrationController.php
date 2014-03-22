<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Query;
use common\helpers\String;

class MigrationController extends \console\components\Controller
{
    private $_d = [
        'yandex_rank' => ['\applications\modules\twitter\components\information\Yandex', 'getRank'],
        'in_yandex'   => ['\applications\modules\twitter\components\information\Yandex', 'getRobot'],
        'google_pr'   => ['\applications\modules\twitter\components\information\Google', 'twitterGetPR'],
    ];

    public function actionModeration()
    {
        $accounts = (new Query())->from('{{%tw_accounts}}')->where(['_status' => 0])->all();

        foreach($accounts as $row) {

            $colums = [];

            foreach($this->_d as $k => $f) {
                $value = call_user_func([$f[0], $f[1]], $row['screen_name']);

                if(isset($value) && $value !== false) {
                    $colums[$k] = $value;

                    if(isset($row[$k])) {
                        $row[$k] = $value;
                    }
                }
            }

            if($row['_mdr'] == 0)
                $row['_mdr'] = 5;

            $itr = \common\api\twitter\Itr::_($row['tweets'], $row['followers'], date("d.m.Y H:i:s", $row['created_at']), $row['listed_count'], $row['yandex_rank'], $row['google_pr'], $row['_mdr']);
            $price = \common\api\twitter\Itr::cost($itr);

            $colums['_status'] = 1;
            $colums['tape'] = 3;

            Yii::$app->db->createCommand()->update('{{%tw_accounts}}', $colums, ['id' => $row['id']])->execute();
            Yii::$app->db->createCommand()->update('{{%tw_accounts_settings}}', ['_price' => $price], ['tid' => $row['id']])->execute();

            echo $row['screen_name'] . PHP_EOL;
            sleep(rand(1, 3));
        }
    }
}