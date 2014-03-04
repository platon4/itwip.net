<?php

use twitter\models\tweets\Filters;

Yii::import('application.modules.twitter.models.tweets.methods.*'); //Загружаем модели всех способах размещение

class TweetsAjaxController extends Controller
{
	public function filters()
	{
		return [
			'accessControl',
			'ajaxOnly'
		];
	}

	public function accessRules()
	{
		return [
			['allow',
				'actions' => ['create', 'GetList', 'SaveList', 'placementMethod', 'getAccounts', 'saveFilter', 'removeFilter', 'GetFastUrls'],
				'roles' => ['user'],
			],
			['deny', // deny all users
				'users' => ['*'],
			],
		];
	}

	/*
	 * Подгружаем модель выброного способа размещение
	 */
	public function actionPlacementMethod()
	{
		$model      = new twitter\models\PlacementMethod;
		$attributes = isset($_GET['m']) ? $_GET['m'] : [];
		$model->load($attributes, true);

		if($model->validate()) {
			if($model->getModel()->validate())
				Html::json(['code' => 200, 'html' => $this->renderPartial($model->getModel()->getViewFile(), ['model' => $model->getModel()], true)]);
			else
				Html::json(['code' => 403, 'message' => $model->getModel()->getError()]);
		}
		else
			Html::json(['code' => 205, 'message' => $model->getError()]);
	}

	/*
	 * Список аккаунтов для ручного размещения
	 *
	 * @return json
	 */
	public function actionGetAccounts($act = '')
	{
		$model = new twitter\models\tweets\methods\Manual;

		/*
		 * Устанавливаем сценарий валидаций
		 */
		if($act == 'rows')
			$model->setScenario('rows');
		else
			$model->setScenario('get');

		if(isset($_POST['subject']) && isset($_POST['Manual']))
			$_POST['Manual']['blogging_topics'] = $_POST['subject'];

		$model->load(array_merge(is_array(Yii::app()->getRequest()->getQuery('a')) ? Yii::app()->getRequest()->getQuery('a') : [], isset($_POST['Manual']) ? $_POST['Manual'] : []), true); //устанавливаем все атрибуты для валидации

		/*
		 * Запускаем валидацию формы
		 */
		if($model->validate())
			Html::json(['code' => 200, 'html' => $this->renderPartial($model->getViewFile(), ['model' => $model], true)]);
		else
			Html::json(['code' => 205, 'message' => $model->getError()]);
	}

	/*
	 * Список ссылок для быстрой индексаций
	 */
	public function actionGetFastUrls($_id)
	{
		$model = new twitter\models\tweets\methods\Fast;
		$model->setScenario('urls');

		$model->load(['_tid' => $_id], true);

		if($model->validate())
			Html::json(['code' => 200, 'html' => $this->renderPartial('/tweets/order/_urlslist', ['model' => $model], true)]);
		else
			Html::json(['code' => 205, 'message' => $model->getError()]);
	}

	/*
	 * Создание заказа
	 *
	 * @return json
	 */
	public function actionCreate($pay = 'now')
	{
		$model = new twitter\models\tweets\Order;
		$model->setScenario('create');

		if(isset($_POST['Order']['data']))
			$_POST['Order']['data']['pay'] = $pay;

		$model->load($_POST);

		if($model->validate())
			Html::json(['code' => 200, 'url' => $model->getRedirectUrl()]);
		else
			Html::json(['code' => 202, 'message' => $model->getError()]);
	}

	/*
	 * Список твитов для ручного добавление
	 *
	 * @return json
	 */
	public function actionGetList()
	{
		$model = new twitter\models\tweets\methods\Manual;

		$model->setScenario('tweets');
		$model->load($_GET, true);

		if($model->validate())
			Html::json(['code' => 200, 'tweets' => $this->renderPartial('/tweets/order/_manualTweetsList', ['tweets' => $model->getTweetsList()], true)]);
		else
			Html::json(['code' => 202, 'message' => $model->getError()]);
	}

	/*
	 * Сохранить выбранный список твитов в соответствующий аккаунт
	 *
	 * @return json
	 */
	public function actionSaveList()
	{
		$model = new twitter\models\tweets\methods\Manual;

		$model->setScenario('save');
		$model->load(array_merge($_GET, $_POST), true);

		if($model->validate() && $model->tweetsListSave())
			Html::json(['code' => 200, 'count' => $model->getTweetsListCount()]);
		else
			Html::json(['code' => 202, 'message' => $model->getError()]);
	}

	/*
	 * Сохранить фильтр
	 *
	 * @return json
	 */
	public function actionSaveFilter()
	{
		$filters = new Filters;

		if($filters->load($_POST['Filter']) && $filters->validate()) {

		}

		Html::json(['code' => 202, 'message' => 'Сохранение фильтров временно не работает.']);
	}

	/*
	 * Удаление фильтра
	 *
	 * @return json
	 */
	public function actionRemoveFilter($id)
	{
		if(!CHelper::int($id))
			Html::json(['code' => 403]);
		else {
			if(Yii::app()->db->createCommand("DELETE FROM {{tw_filters}} WHERE owner_id=:id AND id=:tid AND is_system=0")->execute([':id' => $row['id']]))
				echo json_encode(array('code' => 200));
			else
				echo json_encode(array('code' => 201));
		}
	}
}
