<?php

class officeModule extends CWebModule
{
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'office.models.*',
			'office.components.*',
		));	
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
		
			$_f=Yii::app()->clientScript;

			$_f->registerCssFile($_url . '/css/office.css');
		
			return true;
		}
		else
			return false;
	}
}
