<?php

namespace console\modules\twitter\components;

class Yandex extends \common\components\Yandex
{
    public function hasError()
    {
        return !empty($this->error) && $this->_code != 15 ? true : false;
    }

    public function urlInIndex($url)
    {
        $this->query('url:' . urlencode($url))->request();

        return $this->total() > 0 ? true : false;
    }
} 