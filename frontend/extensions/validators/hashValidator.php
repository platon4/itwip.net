<?php

class hashValidator extends CValidator
{

    public $pattern = "/^[a-zA-Z0-9]{{min},{max}}+$/";
    public $min = 1;
    public $max = 32;
    
    public $allowEmpty = false;

    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        $this->pattern=str_replace('{max}',$this->max,str_replace('{min}',$this->min,$this->pattern));

        if ($this->allowEmpty && $this->isEmpty($value))
            return;

        if ($this->pattern === null)
            throw new CException(Yii::t('yii', 'The "pattern" property must be specified with a valid hash.'));

        if (is_array($value) || (!preg_match($this->pattern, $value)))
        {
            $message = $this->message !== null ? $this->message : Yii::t('yii', 'Your request is invalid.');
            $this->addError($object, $attribute, $message);
        }
    }
}
