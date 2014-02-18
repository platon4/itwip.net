<?php

use twitter\models\Twitter;

class DefaultController extends Controller
{
	public function filters()
	{
		return ['accessControl'];
	}

	public function accessRules()
	{
		return [
			['allow',
				'actions' => ['index', 'getparams'],
				'roles' => ['user'],
			],
			['deny',
				'users' => ['*']
			]
		];
	}

	public function actionGetParams()
	{
		Html::json(['html' => $this->renderPartial('_params', ['model' => new Twitter], true)]);
	}

	public function actionIndex()
	{
		$model = new Twitter;
		$model->load(array_merge($_GET, $_POST));

		if($model->validate()) {
			if(Yii::app()->request->isAjaxRequest)
				Html::json(['html' => $this->renderPartial('_list', ['model' => $model], true)]);
			else
				$this->render('index', ['model' => $model]);
		}
	}
}
