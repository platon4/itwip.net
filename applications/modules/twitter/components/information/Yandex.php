<?php

namespace applications\modules\twitter\components\information;

use Yii;
use common\helpers\Url;
use common\components\Rss;

class Yandex
{
    public static function getRank($login)
    {
        require_once Yii::$app->getBasePath() . '/../common/components/simple_html_dom.php';

        $request = Url::get('http://blogs.yandex.ru/top/twitter/?username=' . $login, [], 'GET', true);
        $html = str_get_html($request['response']);

        if (is_object($html) AND count($html->find('.name-container', 0))) {
            if (count($html->find('.found', 0)) AND strtolower(trim($html->find('.found', 0)->id)) == strtolower(trim($login)))
                $response = intval($html->find('.found', 0)->find('.int', 0)->plaintext);
            else
                $response = 0;

            $html->clear();
        } else
            $response = false;

        unset($html);

        return isset($response) ? $response : false;
    }

    public static function getRobot($login)
    {
        $rss = new Rss();

        if ($_code = $rss->load('http://blogs.yandex.ru/search.rss?journal=' . urlencode('https://twitter.com/' . trim($login))) == 200) {
            $rssArr = $rss->getItems();

            foreach ($rssArr as $item) {
                if (isset($item['author'])) {
                    $time = strtotime($item['pubDate']);

                    if ($time >= (time() - (7 * 86400)))
                        return 1;
                }
            }

            return 0;
        }

        return false;
    }
} 