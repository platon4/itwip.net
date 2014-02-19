<?php

class ajaxController extends Controller
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
		return array(
			array('allow',
				'actions' => array('placementmethod', 'ordercreate', '_getsubjects', '_credentials', '_settings', 'getinfo'),
				'roles' => array('user'),
			),
			array('deny', // deny all users
				'users' => array('*'),
			),
		);
	}

	public function action_getsubjects()
	{
		$bid  = (isset($_POST['t']) AND CHelper::int($_POST['t'])) ? CHelper::int($_POST['t']) : 1;
		$sbj  = (isset($_POST['subject']) AND is_array($_POST['subject'])) ? $_POST['subject'] : array();
		$cmax = 5;

		if($bid AND count($sbj)) {
			if(count($sbj) >= $cmax) {
				$code    = 203;
				$message = Yii::t('twitterModule.accounts', '_subject_add_countmax', array(
					'{count}' => $cmax));
			}
			else {
				$code = 200;

				$subjects = Html::groupByKey(Subjects::model()->findALl(array('order' => 'sort')), 'id', '_key', 'parrent');
				$ids      = array();

				foreach($sbj as $k) {
					if(CHelper::int($k) == 0) {
						$message = Yii::t('twitterModule.accounts', '_subject_add_need_select');
						$code    = 203;
						break;
					}
					else {
						if(in_array($k, $ids)) {
							$message = Yii::t('twitterModule.accounts', '_subject_add_dublicat');
							$code    = 203;
							break;
						}
					}

					$ids[] = $k;
				}

				foreach($subjects as $key => $value) {
					if(in_array($key, $ids)) {
						unset($subjects[$key]);
					}
					else {
						if(is_array($value)) {
							foreach($value as $_k => $_v) {
								foreach($_v as $ak => $av) {
									if(in_array($ak, $ids)) {
										unset($subjects[$key][$_k][$ak]);
									}
								}
							}
						}
					}
				}

				$html = $this->renderPartial('application.modules.twitter.views.default._subjectsDropDownList', array(
					'options' => array('0' => array('disabled' => 'disabled')), 'remove' => 1,
					'bid' => '_subjects_' . $bid, 'subjects' => $subjects), true);
			}
			if($code == 200) {
				echo CJSON::encode(array('code' => $code, 'html' => $html, 'bid' => $bid));
			}
			else {
				echo CJSON::encode(array('code' => $code, 'message' => $message));
			}
		}
		else {
			//throw new CHttpException(403, 'Bad query.');
		}
	}

	/**
	 * Сохранение настройках аккаунта
	 */
	public function action_settings()
	{
		if(isset($_POST['ajax']) AND $_POST['ajax'] == "yes") {
			$tid = (isset($_POST['tid']) AND CHelper::int($_POST['tid'])) ? CHelper::int($_POST['tid']) : 0;

			if($tid) {
				$model = Accounts::model()->findByPk($tid);

				$params = array();
				$rslt   = array();

				switch($_POST['action']) {
				case "s":
					if($model->_status == 7 OR $model->_status == 1) {
						$params['_status'] = ($_POST['s'] == "yes") ? 1 : 7;
						$rslt['_status']   = Html::twStatus($params['_status']);
					}
					break;
				}

				if(count($params)) {
					$model->attributes = $params;

					if($model->validate()) {
						$model->save();
						echo CJSON::encode(array_merge(array('code' => 200), $rslt));
					}
					else {
						echo Html::errorSummary($model);
					}
				}
				else {
					throw new CHttpException(403, 'Bad query.');
				}
			}
			else {
				throw new CHttpException(404, 'Account not found.');
			}
		}

		Yii::app()->end();
	}

	public function actionGetinfo()
	{
		$tid = (isset($_POST['tid']) AND CHelper::int($_POST['tid'])) ? CHelper::int($_POST['tid']) : 0;

		if($tid) {
			$sth = Yii::app()->db->createCommand("SELECT `a`.`id`, `a`.`screen_name`, `a`.`name`, `a`.`avatar`, `a`.`created_at`, `a`.`_lang`, `a`.`date_add`, `a`.`itr`, `a`.`in_google`, `a`.`google_pr`, `a`.`in_yandex`, `a`.`yandex_rank`, `a`.`tweets`, `a`.`following`, `a`.`followers`, `a`.`white_list`, `a`.`black_list`, `a`.`_posts_count`, `s`.`_price`, `s`.`_age`, `s`.`_gender`, `s`.`_subjects`,`s`.`working_in` FROM {{tw_accounts}} `a` LEFT JOIN {{tw_accounts_settings}} `s` ON `a`.`id`=`s`.`tid` WHERE `a`.`id`=:id");

			$sth->bindParam(':id', $tid, PDO::PARAM_INT);
			$dataRead = $sth->query();
			$row      = $dataRead->read();

			if($row['id']) {
				$ageData = require Yii::app()->getModulePath() . '/twitter/data/_age.php';
				$_s      = '';

				if($row['_subjects'] != 0) {
					$_subjects = explode(",", $row['_subjects']);
					$_sb       = Subjects::model()->findAll(array('order' => 'sort'));
					$_b        = array();

					foreach($_sb as $_v) {
						if(in_array($_v['id'], $_subjects)) {
							$_b[] = Yii::t('twitterModule.accounts', $_v['_key']);
						}
					}

					$_s = implode(", ", $_b);
				}
				else
					$_s = Yii::t('twitterModule.accounts', '_no_subject_align');

				$this->renderPartial('getinfo', array('row' => $row, '_age' => $ageData,
					'subjects' => $_s));
			}
			else
				echo json_encode(array('code' => 404, 'message' => 'Account not found.'));
		}
		else
			echo json_encode(array('code' => 403, 'message' => Yii::t('yii', 'Your request is invalid.')));

		Yii::app()->end();
	}

	/**
	 * Обновление данных аккаунта ajax
	 */
	public function action_credentials($_return = false)
	{
		$_default     = '<span onclick="Settings._credentials(this); return false;">' . Yii::t('twitterModule.accounts', '_twitterAccountSetting_check') . '</span>';
		$allow_action = array('yandex_rank', 'in_yandex', 'google_pr', 'all'); // 'in_google',

		if(isset($_POST['tid']) AND CHelper::int($_POST['tid']) AND isset($_POST['_check']) AND in_array($_POST['_check'], $allow_action)) {
			$account = Yii::app()->db->createCommand("SELECT a.id,a.itr,a.in_yandex,a.yandex_rank,a.google_pr,a.tweets,a.followers,a.created_at,a.listed_count,a.app,a._mdr,s._price FROM {{tw_accounts}} a INNER JOIN {{tw_accounts_settings}} s ON a.id=s.tid   WHERE a.id=:id")->queryRow(true, array(
				':id' => $_POST['tid']));

			if($account !== false) {
				$data = array('_s[_key]' => md5(Yii::app()->params['twitter']['secret_key'] . $account['app'] . $account['id']),
					'_s[_tid]' => $account['id']);

				if(isset($_POST['_check']) AND !empty($_POST['_check']))
					$data['_s[_check]'] = $_POST['_check'];

				$app = Yii::app()->db->createCommand("SELECT _url FROM {{tw_application}} WHERE id=:id")->queryRow(true, array(
					':id' => $account['app']));

				$request  = CHelper::_getURL($app['_url'] . '/twitter/stats/_get', "POST", $data);
				$response = CJSON::decode($request['response']);

				if($request['code'] == 200 AND isset($response['code'])) {
					if($response['code'] == 200) {
						$code = 200;
						$_y   = array('in_yandex', 'in_google');
						$html = Yii::t('twitterModule.accounts', '_twitterAccountSetting_recentlyTested');

						$fields = array('yandex_rank' => 'yandex_rank', 'in_yandex' => 'in_yandex',
							'google_pr' => 'google_pr');

						$updates = array();
						$values  = array(':id' => $account['id']);

						foreach($response['stats'] as $k => $stats) {
							if(array_key_exists($k, $fields)) {
								if($stats['code'] == 200) {
									$updates[]                 = $fields[$k] . '=:' . $fields[$k];
									$values[':' . $fields[$k]] = $stats['value'];
									$account[$fields[$k]]      = $stats['value'];

									if(in_array($k, $_y)) {
										$result = ($stats['value'] == 1) ? Yii::t('main', '_yes') : Yii::t('main', '_no');
									}
									else {
										$result = $stats['value'];
									}
								}
								else if($response['_check'] != "all") {
									$code = 99;
								}
							}
						}

						$itr    = THelper::itr($account['tweets'], $account['followers'], date("d.m.Y H:i:s", $account['created_at']), $account['listed_count'], $account['yandex_rank'], $$account['google_pr'], $account['_mdr']);
						$_price = THelper::itrCost($itr);

						$updates[]      = 'itr=:itr';
						$values[':itr'] = $itr;

						if(round($account['_price']) < 1)
							Yii::app()->db->createCommand("UPDATE {{tw_accounts_settings}} SET _price=:price WHERE tid=:id")->execute(array(
								':id' => $account['id'], ':price' => $_price));

						Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET " . implode(", ", $updates) . " WHERE id=:id")->execute($values);

						if($response['_check'] == "all")
							echo CJSON::encode(array('code' => $code));
						else
							echo CJSON::encode(array('code' => $code, 'result' => $result,
								'html' => $html));
					}
					else {
						switch($response['code']) {
						case "202":
							$checkType = array(
								'in_yandex' => Yii::t('twitterModule.accounts', '_index_yandex'),
								'yandex_rank' => Yii::t('twitterModule.accounts', '_rank_acc'),
								'google_pr' => Yii::t('twitterModule.accounts', '_rank_acc'),
							);

							$messages = Yii::t('twitterModule.accounts', '_update_error_202', array(
								'{type}' => $checkType[$_POST['_check']],
								'{time}' => ceil(Yii::app()->params['twitter']['update_interval'][$_POST['_check']] / 60)));
							break;

						case "33":
							$messages = Yii::t('twitterModule.accounts', '_update_error_33');
							break;

						default:
							$messages = (trim($response['messages']) != NULL) ? $response['messages'] : Yii::t('twitterModule.settings', '_update_error_0');
						}

						echo CJSON::encode(array('code' => $response['code'], 'html' => $_default,
							'messages' => $messages));
					}
				}
				else
					echo CJSON::encode(array('code' => 201, 'messages' => Yii::t('twitterModule.accounts', '_error_connect_to_server'),
						'html' => $_default));
			}
			else
				echo CJSON::encode(array('code' => '404', 'message' => 'Account not found.',
					'html' => $_default));
		}
		else
			echo CJSON::encode(array('code' => '403', 'message' => Yii::t('internal', '_twitterAccountSetting_wrong_params'),
				'html' => $_default));

		Yii::app()->end();
	}

	public function actionOrderCreate($pay)
	{
		$model  = new OrderTweets;
		$params = isset($_POST['Order']) ? $_POST['Order'] : [];

		$model->attributes = $model->at = array_merge($params, ['when' => $pay]);

		if($model->validate())
			Html::json(array('url' => '/twitter/tweets/status', 'code' => 200, 'messages' => Yii::t('twitterModule.tweets', '_order_save_ok')));
		else
			Html::json(['code' => 203, 'messages' => $model->getError()]);
	}

}
