<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Query;
use common\helpers\String;

class MigrationController extends \console\components\Controller
{
    private $_d = [
        'yandex_rank' => ['application\modules\twitter\components\information\Yandex', 'getRank'],
        'in_yandex'   => ['application\modules\twitter\components\information\Yandex', 'getRobot'],
        'google_pr'   => ['application\modules\twitter\components\information\Google', 'twitterGetPR'],
    ];

    public function actionIndex()
    {
        $accounts = (new Query())->from('{{%tw_accounts}}')->where(['_status' => 0])->limit(1)->all();

        foreach($accounts as $row) {

            $colums = [];

            foreach($this->_d as $k => $f) {
                $value = call_user_func([$f[0], $f[1]], $row['screen_name']);

                if(isset($value) && $value !== false) {
                    $colums[] = $k . ':' . $k;
                    $values[':' . $k] = $value;
                }
            }

            print_r($colums);
            die();
            $this->get()->itr = \THelper::itr($this->get('tweets'), $this->get('followers'), date("d.m.Y H:i:s", $this->get('created_at')), $this->get('listed_count'), $this->get('yandex_rank'), $this->get('google_pr'), $this->get('_mdr'));
            $this->get('', 'settings')->_price = \THelper::itrCost($this->get('itr'));

            print_r($row);
        }
    }
}