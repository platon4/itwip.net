<?php

class FormModel extends CFormModel
{
	public $stopOnError = true;
	public $_code = 0;
	protected $_attributes = [];

	public function validate($attributes = NULL, $clearErrors = true)
	{
		if($clearErrors)
			$this->clearErrors();

		if($this->beforeValidate()) {
			foreach($this->getValidators() as $validator) {
				$validator->validate($this, $attributes);

				if($this->stopOnError AND $this->hasErrors())
					break;
			}

			if(!$this->hasErrors())
				$this->afterValidate();

			return !$this->hasErrors();
		}
		else
			return false;
	}

	public function getError($attribute = NULL)
	{
		if($attribute === NULL) {
			if(is_array(parent::getErrors())) {
				foreach(parent::getErrors() as $att => $message)
					return $message[0];
			}
			else
				return NULL;
		}
		else
			return parent::getError($attribute);
	}

	public function getCode()
	{
		return $this->_code;
	}

	public function setCode($code)
	{
		$this->_code = $code;
		return $this;
	}

	public function formName()
	{
		$reflector = new ReflectionClass($this);
		return $reflector->getShortName();
	}

	public function setAttributes($values, $safeOnly = true)
	{
		if(!is_array($values))
			return;

		$this->_attributes = $values;
		$attributes        = array_flip($safeOnly ? $this->getSafeAttributeNames() : $this->attributeNames());
		foreach($values as $name => $value) {
			if(isset($attributes[$name]))
				$this->$name = $value;
			elseif($safeOnly)
				$this->onUnsafeAttribute($name, $value);
		}
	}

	public function getAttributes($names = NULL, $all = false)
	{
		if($all === true)
			return $this->_attributes;

		$values = array();
		foreach($this->attributeNames() as $name)
			$values[$name] = $this->$name;

		if(is_array($names)) {
			$values2 = array();
			foreach($names as $name)
				$values2[$name] = isset($values[$name]) ? $values[$name] : NULL;
			return $values2;
		}
		else
			return $values;
	}

	public function load($data, $no_scope = false)
	{
		$this->beforeLoad();
		$scope = $this->formName();
		if($scope == '' || $no_scope) {
			$this->setAttributes($data);
			return true;
		}
		elseif(isset($data[$scope])) {
			$this->setAttributes($data[$scope]);
			return true;
		}
		else {
			return false;
		}
	}

	public function beforeLoad()
	{

	}
}
