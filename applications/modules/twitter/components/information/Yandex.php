<?php

namespace app\modules\twitter\components\information;

use Yii;
use common\helpers\Url;

class Yandex
{
    public static function getRank($login)
    {
        require_once Yii::$app->getBasePath() . '/../common/components/simple_html_dom.php';

        $request = Url::get('http://blogs.yandex.ru/top/twitter/?username=' . $login, [], 'GET', true);
        $html = str_get_html($request['response']);

        if (is_object($html) AND count($html->find('.name-container', 0))) {
            if (count($html->find('.found', 0)) AND strtolower(trim($html->find('.found', 0)->id)) == strtolower(trim($login)))
                $yandex_rank = intval($html->find('.found', 0)->find('.int', 0)->plaintext);

            $response = $yandex_rank;
            $html->clear();
        } else
            $response = false;

        unset($html);

        return $response;
    }

    public static function getRobot()
    {
        return false;
    }
} 