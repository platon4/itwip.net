<?php

namespace twitter\models\tweets;

use Yii;

class Order extends \FormModel
{
	public $data;
	public $method;
	protected $redirectUrl;

	public function rules()
	{
		return [
			['data', 'safe', 'on' => 'create'],
			['method', 'in', 'range' => ['fast', 'manual'], 'allowEmpty' => false]
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'method' => 'Способ размещения',
		];
	}

	public function afterValidate()
	{
		if($this->getScenario() == 'create')
			$this->create();
		else
			$this->addError('action', 'Unknown action');
	}

	protected function create()
	{
		$actions = [
			'fast' => 'twitter\models\tweets\methods\Fast',
			'manual' => 'twitter\models\tweets\methods\Manual'
		];

		if(isset($actions[$this->method])) {
			$model = new $actions[$this->method];
			$model->setScenario('order');
			$model->setAttributes($this->data);

			if($model->validate()) {
				if($model->create()) {
					$this->redirectUrl = $model->getRedirectUrl();
					$model->clear();
				}
				else
					$this->addError('error', $model->getError());;
			}
			else
				$this->addError('error', $model->getError());
		}
	}

	/*
	 * Возвращаем ссылку для перенаправление пользователя после создание заказа
	 */
	public function getRedirectUrl() { return $this->redirectUrl; }
}
