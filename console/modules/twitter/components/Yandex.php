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
        $this->query('url:' . $url)->request();

        $count = $this->total();
        $this->yandexFliush();

        Logger::log($this->error . " Count " . $count . "  " . $this->_code . "  " . urldecode($url), 'daemons/indexes-yandex');

        return $count && $this->_code != 15 > 0 ? true : false;
    }

    protected function yandexFliush()
    {
        $this->response;
        $this->wordstat = array();
        $this->results = array();
        $this->total = null;
    }
} 