<?php

Yii::import('application.modules.twitter.models.tweets.methods.*');

use twitter\models\tweets\Create;
use twitter\models\tweets\Roster;
use twitter\models\tweets\Prepared;
use twitter\models\tweets\Finality;

class TweetsController extends Controller
{
    public $activeMenu = 'tw_adv';

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => ['finality', 'collection', 'roster', 'fulfilled', 'request', 'processing', 'index', 'prepared'],
				'roles' => ['user'],
			),
			array('deny', // deny all users
				'users' => ['*'],
			),
		);
	}

	public function actionIndex()
	{
		$this->render("index");
	}

	public function actionRequest($act = '', $id = 0)
	{
        $this->activeMenu = 'tw_exe';

		if(intval($id) AND Yii::app()->request->isAjaxRequest) {
			$order = Yii::app()->db->createCommand("SELECT tt.*,a.owner_id FROM {{tweets_to_twitter}} tt INNER JOIN {{tw_accounts}} a ON tt._tw_account=a.id WHERE tt.id=:id LIMIT 1")->queryRow(true, array(
				':id' => $id));

			if(count($order) AND $order['owner_id'] == Yii::app()->user->id) {
				$code    = 0;
				$message = '';

				if($act == 'approve') {
					if($order['approved'] == 0) {
						Yii::app()->db->createCommand("UPDATE {{tweets_to_twitter}} SET approved=1 WHERE id=:id")->execute(array(
							':id' => $order['id']));
						$code    = 200;
						$message = 'Заказ успешно отправлен на автоматическое размещение.';
					}
					else {
						$code    = 1;
						$message = 'Данный заказ уже подтвержден';
					}
				}
				else if($act == 'refuse') {
					if($order['approved'] == 0) {
						Yii::app()->db->createCommand("UPDATE {{tweets_to_twitter}} SET status=1 WHERE id=:id")->execute(array(
							':id' => $order['id']));
						$code    = 200;
						$message = 'Заказ успешно отклонен.';
					}
					else {
						$code    = 2;
						$message = 'Данный заказ уже подтвержден, отклонить невозможно.';
					}
				}

				echo json_encode(array('code' => $code, 'messages' => $message));
				Yii::app()->end();
			}
			else
				throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
		}

		$rows = Yii::app()->db->createCommand("SELECT tt.*,a.screen_name,a.name,a.avatar FROM {{tweets_to_twitter}} tt INNER JOIN {{tw_accounts}} a ON tt._tw_account=a.id WHERE tt.approved=0 AND tt.status=0 AND a.owner_id=:id LIMIT 50")->queryAll(true, array(':id' => Yii::app()->user->id));

		if(Yii::app()->request->isAjaxRequest) {
			echo json_encode(array('code' => 200, 'html' => array($this->renderPartial('_request_rows', array(
				'rows' => $rows), true))));
			Yii::app()->end();
		}
		else {
			$this->render('request', array('rows' => $rows));
		}
	}

	public function actionFulfilled($tid = 0, $_o = '', $_a = '')
	{
        $this->activeMenu = 'tw_exe';

		$model             = new Fulfilled;
		$model->attributes = array(
			'tid' => $tid,
			'order' => $_o,
			'sort' => $_a,
		);

		if($model->validate()) {
			if(Yii::app()->request->isAjaxRequest) {
				echo json_encode(array('tid' => $model->tid, 'html' => $this->renderPartial('_fulfilled_rows', array(
						'rows' => $model->_getRows(), 'pages' => $model->pages), true)));
				Yii::app()->end();
			}
			else
				$this->render('fulfilled', array('tid' => $model->tid, 'rows' => $model->_getRows(),
					'pages' => $model->pages));
		}
		else {
			$this->_message(Yii::t('main', 'invalid_request'), Yii::t('main', '_error'));
		}
	}

	public function actiontcollection($uid)
	{
		if(CHelper::validID($uid)) {
			$parser = new Parser();
			$i      = 0;
			$db     = Yii::app()->db;
			$db->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE, true);

			$command = $db->createCommand();

			$data = $command
				->select('id,_url,_template,_excule_words')
				->from('{{tw_tweets_sitemap }}')
				->where('_uid=:uid AND _status=0', array(':uid' => $uid))
				->limit(15)
				->queryAll();

			$update = $db->createCommand('UPDATE `{{tw_tweets_sitemap}}` SET `_status`=:_status,`_text`=:_text,`_ecode`=:_ecode WHERE id=:id');
			CHelper::createFile('tw_' . $uid . '.txt', '', Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . '/tmp');

			foreach($data as $std) {
				$parser->init(trim($std['_url']), true);

				if($parser->validate()) {
					$text   = CHelper::toUnicode($parser->get('title', 'inner'));
					$status = 1;

					$text = str_replace(explode(",", $std['_excule_words']), '', $text);

					if(preg_match('{url}', $std['_template']) OR preg_match('{title}', $std['_template'])) {
						$_text = $std['_template'];
						$_text = str_replace('{title}', $text, $_text);
						$_text = str_replace('{url}', $std['_url'], $_text);
					}
					else
						$_text = $text . ' ' . $std['_url'];
				}
				else {
					$ecode  = $parser->getError('code');
					$_text  = $parser->getError('error');
					$status = 2;
				}

				$update->execute(array(':_status' => $status, ':_text' => $_text, ':_ecode' => $ecode,
					':id' => $std['id']));

				$parser->clear();

				if($parser->getError('code') == 28) {
					sleep(rand(1, 2));
				}
				$i++;
			}

			CHelper::removeFile('tw_' . $uid . '.txt', Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . '/tmp');
		}
		Yii::app()->end();
	}

	public function actionPrepared($action = '')
	{
		$tw = new Prepared;

		if(!CHelper::isEmpty($action))
			$tw->scenario = $action;

		$tw->load($_GET);

		if($tw->validate()) {
			if($tw->getView() === true) {
				if(Yii::app()->request->isAjaxRequest)
					Html::json(['code' => 200, 'count' => $tw->rosterCount(), 'html' => $this->renderPartial($tw->getViewFile(true), ['model' => $tw], true), 'message' => $tw->getMessage()]);
				else
					$this->render($tw->getViewFile(), array('model' => $tw));
			}
		}
		else {
			if(Yii::app()->request->isAjaxRequest)
				Html::json(['code' => $tw->getCode(), 'message' => $tw->getError()]);
			else
				$this->_message($tw->getError(), Yii::t('main', '_error'), '/twitter/tweets/collection');
		}
	}

	/*
	 * Создание, сбор твитов
	 */
	public function actionCollection()
	{
        $tw = new Create;

		if($tw->load($_POST)) {
			if($tw->validate()) {
				if(Yii::app()->request->isAjaxRequest)
					Html::json(array('code' => 301, 'url' => Yii::app()->homeUrl . 'twitter/tweets/roster?_tid=' . $tw->getHash()));
				else
					$this->redirect(Yii::app()->homeUrl . '/twitter/tweets/roster?_tid=' . $tw->getHash());
			}
			else {
				if(Yii::app()->request->isAjaxRequest)
					Html::json(array('code' => 205, 'message' => $tw->getError()));
				else
					Yii::app()->user->setFlash('_COLLECTION_MSG', array('type' => 'error', 'message' => $tw->getError()));
			}
		}

		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/www-twitter-zTrE2z.js', CClientScript::POS_END);
		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jfileupload.js', CClientScript::POS_END);

		$this->render('_collection');
	}

	/*
	 * Список собраных твитов
	 */
	public function actionRoster($_tid, $group = NULL, $action = 'get')
	{
		$v         = new Roster;
		$scenarios = ['saveRoster' => 'saveRoster'];

		if(array_key_exists($action, $scenarios))
			$v->setScenario($scenarios[$action]);

		$v->load([
			'_tid' => $_tid,
			'_group' => $group,
			'_action' => $action,
			'ids' => (isset($_POST['tweets'])) ? $_POST['tweets'] : [],
			'_title' => (isset($_POST['title'])) ? $_POST['title'] : NULL,
			'edit' => (isset($_POST['Edit'])) ? $_POST['Edit'] : [],
		], true);

		if($v->validate()) {
			if(Yii::app()->request->isAjaxRequest) {
				$respons = [];

				if($v->getAction() === 'info')
					$respons = ['code' => 200, 'next' => $v->allowPlace(), 'info' => $this->renderPartial('_roster_information', ['model' => $v], true)];
				elseif($v->getAction() === 'get')
					$respons = ['code' => 200, 'next' => $v->allowPlace(), 'tweets' => $this->renderPartial('_roster_rows', array('model' => $v), true)];
				elseif($v->getAction() === 'tweetEdit')
					$respons = ['code' => 200, 'next' => $v->allowPlace(), 'message' => Yii::t('twitterModule.tweets', '_tweet_success_save'), 'info' => $this->renderPartial('_roster_information', array('model' => $v), true), 'tweets' => $this->renderPartial('_roster_rows', array('model' => $v), true)];
				elseif($v->getAction() === 'saveRoster')
					$respons = ['code' => 200];
				else
					$respons = ['code' => 200, 'next' => $v->allowPlace(), 'info' => $this->renderPartial('_roster_information', array('model' => $v), true), 'tweets' => $this->renderPartial('_roster_rows', ['model' => $v], true)];

				Html::json($respons);
			}
			else {
				Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/www-twitter-zTrE2z.js', CClientScript::POS_END);
				Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/jfileupload.js', CClientScript::POS_END);

				$this->render('_roster', ['model' => $v]);
			}
		}
		else {
			if(Yii::app()->request->isAjaxRequest) {
				if($v->getCode() === 301) {
					Yii::app()->user->setFlash('TWEETS_MESSAGE', $v->getError());
					Html::json(['code' => $v->getCode(), 'url' => '/twitter/tweets/collection']);
				}
				else
					Html::json(['code' => $v->getCode(), 'message' => $v->getError(), 'next' => $v->allowPlace()]);
			}
			else {
				if($v->getCode() === 301) {
					Yii::app()->user->setFlash('TWEETS_MESSAGE', $v->getError());
					$this->redirect('/twitter/tweets/collection');
				}
				else
					$this->_message($v->getError(), Yii::t('main', '_error'), '/twitter/tweets/collection');
			}
		}
	}

	/*
	 * Выбор аккаунтов и создание заказа
	 */
	public function actionFinality($_tid)
	{
		$model = new Finality;

		if($model->load(['_tid' => $_tid], true) && $model->validate())
			$this->render('_finality', ['model' => $model]);
		else
			$this->_message($model->getError(), Yii::t('main', '_error'), '/twitter/tweets/collection');
	}
}
