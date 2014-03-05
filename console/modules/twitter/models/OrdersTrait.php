<?php

namespace console\modules\twitter\models;

trait OrdersTrait
{
    protected $_data;
    protected $_params;
    protected $_updates = [];
    protected $_taks = [];

    /*
     * Обработка заказа
     */
    public function process(array $data)
    {
        $this->_data = $data;
        $this->make();

        return [
            'update' => $this->getUpdate(),
            'taks'   => $this->getTaks()
        ];
    }

    public function get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function getParam($key)
    {
        if($this->_params === null) {
            $this->_params = json_decode($this->_data['_params'], true);
        }

        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }

    public function getUpdate()
    {
        return $this->_updates;
    }

    public function getTaks()
    {
        return $this->_taks;
    }
} 