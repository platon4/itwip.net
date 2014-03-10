<?php

namespace console\modules\twitter\components;

class Yandex extends \console\components\Yandex
{
    public function hasErrors()
    {
        return !empty($this->error) ? true : false;
    }

    public function urlInIndex($url)
    {
        $this->query('url:' . urlencode($url))->request();
        echo $url."\n";
        echo $this->total()."\n";
        return $this->total() > 0 ? true : false;
    }
} 