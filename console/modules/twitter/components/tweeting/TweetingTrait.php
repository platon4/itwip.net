<?php

namespace console\modules\twitter\components\tweeting;

use yii\base\Exception;

trait TweetingTrait
{
    protected $_validators;
    protected $task;
    protected $_params;

    protected function setValidators($validators)
    {
        $this->_validators = (array) $validators;
    }

    protected function validate()
    {
        if($this->_validators !== null && !empty($this->_validators)) {
            foreach($this->_validators as $validator) {
                $validator = 'validate' . str_replace(' ', '', ucwords(implode(' ', explode('-', $validator))));
                if(method_exists($this, $validator)) {
                    if($this->$validator() !== true)
                        return false;
                } else
                    throw new Exception('Method "' . $validator . '" not exists in class ' . get_called_class());
            }

            return true;
        } else
            return true;
    }

    protected function init($task)
    {
        $this->task = $task;

        if($this->validate()) {
            $this->execute();
        }
    }

    public function getTask()
    {
        return $this->task;
    }

    public function get($key)
    {
        return isset($this->task[$key]) ? $this->task[$key] : null;
    }

    public function getOwner()
    {
        return $this->getParams('order_owner');
    }

    public function getParams($key)
    {
        if($this->_params === null) {
            $this->_params = json_decode($this->get('params'), true);
        }

        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }

    public function getTimeoutInterval()
    {
        return rand(3, 15) * 60;
    }

    public function getTweet()
    {
        return $this->getParams('tweet');
    }
}