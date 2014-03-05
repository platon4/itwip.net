<?php

namespace console\modules\twitter\models;

trait OrdersTrait
{
    protected $_data;
    protected $_params;
    protected $_update = [];
    protected $_task = [];
    protected $_interval;
    protected $_processDate;

    /*
     * Обработка заказа
     */
    public function process(array $data)
    {
        $this->_data = $data;
        $this->make();

        return [
            'update' => $this->getUpdate(),
            'taks'   => $this->getTask()
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
        return $this->_update;
    }

    public function getTask()
    {
        return $this->_task;
    }

    public function setProcessDate($date)
    {
        $this->_update['order'][$this->get('id')]['process_date'] = $date;
    }

    public function clear()
    {
        $this->_interval = null;
        $this->hCount = 0;
        $this->hours = [];
        $this->_update = [];
        $this->_task;
        $this->_processDate = null;
    }
} 