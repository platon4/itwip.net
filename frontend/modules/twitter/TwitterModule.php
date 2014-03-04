<?php

class TwitterModule extends CWebModule
{
	public $urlRules = [
		'twitter/tweets/ajax/<action:\w+>' => 'twitter/tweetsAjax/<action>',
	];

	public function init()
	{
		$this->setImport([
			$this->id . '.models.*',
			$this->id . '.components.*',
		]);
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action)) {
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/tw-core.js');
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/www-twitter-zTrE2z.js', CClientScript::POS_HEAD);

			return true;
		}
		else
			return false;
	}
}
