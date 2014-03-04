<?php

class actionsValidator extends CValidator
{

    public $actions = array();
    public $allowEmpty = false;

    protected function validateAttribute($object, $attribute)
    {
        $this->setValue($object, $attribute);

        if ($this->allowEmpty && $this->isEmpty($object->getScenario()))
            return;

        if (!in_array($object->getScenario(), $this->actions) || $this->isEmpty($this->actions))
        {
            if (!$this->isEmpty($object->getScenario()) && array_key_exists($object->getScenario(), $this->actions))
                return;

            $message = $this->message !== null ? $this->message : Yii::t('yii', 'Your request is invalid.');
            $this->addError($object, $attribute, $message);
        }
    }

    protected function setValue($object, $attribute)
    {
        $object->$attribute = isset($this->actions[$object->getScenario()]) ? $this->actions[$object->getScenario()] : $object->getScenario();
    }
}
