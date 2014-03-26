<?php

namespace console\modules\twitter\components;

use console\components\Logger;

class Yandex extends \common\components\Yandex
{
    public function hasError()
    {
        return !empty($this->error) && $this->_code != 15 ? true : false;
    }

    public function urlInIndex($url)
    {
        $this->query('url:' . urlencode($url))->request();

        $count = $this->total();

        Logger::log($this->error . " Count " . $count . "  " . $this->_code . "  " . urlencode($url), 'daemons/yandex');

        return $count > 0 ? true : false;
    }
} 