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
				'actions' => ['index', 'resetparams', 'bwlist'],
				'roles' => ['user'],
			],
			['deny',
				'users' => ['*']
			]
		];
	}

	public function actionResetParams()
	{
		Html::json(['html' => $this->renderPartial('_params', ['model' => new Twitter], true)]);
	}

	public function actionIndex()
	{
		$model = new Twitter;
		$model->load($_POST);

		if($model->validate()) {
			if(Yii::app()->request->isAjaxRequest)
				Html::json(['code' => 200, 'html' => $this->renderPartial('_list', ['model' => $model], true), 'stats' => $this->renderPartial('_stats', ['model' => $model], true)]);
			else
				$this->render('index', ['model' => $model]);
		}
		else
			$this->_message($model->getError(), '', '/twitter');
	}

	public function actionBwList()
	{
		$model = new Twitter;
		$model->setScenario('bw');

		$model->load($_POST, true);

		if($model->validate() && $model->bwToggle()) {
			Html::json(['code' => 200, 'stats' => $this->renderPartial('_stats', ['model' => $model], true), 'white_count' => $model->getStat()['white_list'], 'black_count' => $model->getStat()['black_list']]);
		}
		else
			Html::json(['code' => 201, 'message' => $model->getError()]);
	}
}
