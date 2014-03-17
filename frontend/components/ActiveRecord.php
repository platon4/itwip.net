<?php

/**
 * Description of ActiveRecord
 *
 * @author Александр
 */
class ActiveRecord extends CActiveRecord
{
    public function validate($attributes = null, $clearErrors = true, $allErrors = false)
    {
        if($clearErrors)
            $this->clearErrors();
        if($this->beforeValidate()) {
            foreach($this->getValidators() as $validator) {
                if(!$allErrors AND $this->hasErrors())
                    break;

                $validator->validate($this, $attributes);
            }

            $this->afterValidate();
            return !$this->hasErrors();
        } else
            return false;
    }

    public function formName()
    {
        $reflector = new ReflectionClass($this);
        return $reflector->getShortName();
    }

    public function load($data, $no_scope = false)
    {
        $this->beforeLoad();
        $scope = $this->formName();

        if($scope == '' || $no_scope) {
            $this->setAttributes($data);
            return true;
        } elseif(isset($data[$scope])) {
            $this->setAttributes($data[$scope]);
            return true;
        } else {
            return false;
        }
    }

    public function getError($key = null)
    {
        if($key === null) {
            $error_text = '';

            if(is_array(parent::getErrors())) {
                foreach(parent::getErrors() as $k => $t) {
                    $error_text = $t[0];
                    break;
                }
            }

            return $error_text;
        } else
            return parent::getError($key);
    }

    public function beforeLoad()
    {

    }
}
