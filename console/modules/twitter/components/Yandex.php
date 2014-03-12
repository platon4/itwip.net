<?php

namespace console\modules\twitter\components;

class Yandex extends \common\components\Yandex
{
    public function hasErrors()
    {
        return !empty($this->error) ? true : false;
    }

    public function urlInIndex($url)
    {
        $this->query('url:' . urlencode($url))->request();

        return $this->total() > 0 ? true : false;
    }
} 