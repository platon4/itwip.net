<?php

namespace console\modules\twitter\models;

use yii\db\Query;

trait OrdersTrait
{
    protected $_data;
    protected $_params;
    protected $_update = [];
    protected $_task = [];
    protected $_interval;
    protected $_processDate;
    protected $taskCount;

    /*
     * Обработка заказа
     */
    public function process(array $data)
    {
        $this->_data = $data;
        $this->make();

        return [
            'update' => $this->getUpdate(),
            'task'   => $this->getTask()
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

    public function _setTaskParams($data)
    {
        if(isset($data['_params']))
            $this->_taskParams = json_decode($data['_params'], true);
    }

    public function _getTaskParams($key)
    {
        return isset($this->_taskParams[$key]) ? $this->_taskParams[$key] : '';
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

    public function getDaemon()
    {
        if($this->taskCount === 0) {
            $this->taskCount = (new Query())->from('{{%twitter_tweeting}}')->count();
        }

        $daemon = round($this->taskCount / 5000);

        return $daemon == 0 ? 0 : $daemon;
    }
}