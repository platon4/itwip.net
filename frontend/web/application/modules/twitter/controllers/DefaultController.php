<?php

class DefaultController extends Controller
{
	public $limitList = array(0 => array('title' => '10', 'value' => '10'), 1 => array('title' => '20', 'value' => '20'), 2 => array('title' => '30', 'value' => '30'), 3 => array('title' => '40', 'value' => '40'), 4 => array('title' => '50', 'value' => '50'));

	public function filters()
	{
		return array('accessControl',);
	}

	public function accessRules()
	{
		return array(array('allow', 'actions' => array('index', 'getparams'), 'roles' => array('user'),), array('deny', // deny all users
			'users' => array('*'),),);
	}

	public function actionGetParams()
	{
		$ageData  = require Yii::app()->getModulePath() . '/twitter/data/_age.php';
		$subjects = Html::groupByKey(Subjects::model()->findALl(array('order' => 'sort')), 'id', '_key', 'parrent');

		echo json_encode(array('html' => $this->renderPartial('_params', array('subjects' => $subjects, 'ageData' => $ageData), true)));
	}

	public function actionIndex()
	{
		//if(!Yii::app()->user->checkAccess('admin'))
		//$this->_message('Страница временно закрыта на реконструкцию.');

		$uid    = Yii::app()->user->id;
		$crt    = array();
		$ordArr = array('date' => array('o' => '`a`.`date_add`'), 'itr' => array('o' => '`a`.`itr`'), 'wlist' => array('o' => '`a`.`white_list`'), 'blist' => array('o' => '`a`.`black_list`'), 'cpost' => array('o' => '`a`.`_posts_count`'), 'price' => array('o' => '`s`.`_price`'), 'tape' => array('o' => '`a`.`tape`'), 'followers' => array('o' => '`a`.`followers`'),);

		$crt[]  = '`a`.`_status`=\'1\'';
		$params = array('itr_ot' => array('c' => '`a`.`itr`', 'w' => '>=', 't' => 'int'), 'itr_do' => array('c' => '`a`.`itr`', 'w' => '<=', 't' => 'int'), 'glp_ot' => array('c' => '`a`.`google_pr`', 'w' => '>=', 't' => 'int'), 'glp_do' => array('c' => '`a`.`google_pr`', 'w' => '<=', 't' => 'int'), 'yav_ot' => array('c' => '`a`.`yandex_rank`', 'w' => '>=', 't' => 'int'), 'yav_do' => array('c' => '`a`.`yandex_rank`', 'w' => '<=', 't' => 'int'), 'in_google' => array('c' => '`a`.`in_google`', 'w' => '=', 't' => 'enum'), 'in_yandex' => array('c' => '`a`.`in_yandex`', 'w' => '=', 't' => 'enum'), 'post_price_ot' => array('c' => '`s`.`_price`', 'w' => '>=', 't' => 'int'), 'post_price_do' => array('c' => '`s`.`_price`', 'w' => '<=', 't' => 'int'), 'time_add' => array('c' => '`a`.`date_add`', 'w' => '>=', 't' => 'days'), 'age_ot' => array('c' => '`a`.`created_at`', 'w' => '<=', 't' => 'days'), 'age_do' => array('c' => '`a`.`created_at`', 'w' => '>=', 't' => 'days'), '_gender' => array('c' => '`s`.`_gender`', 'w' => '=', 't' => 'int'), 'pType' => array('c' => '`s`.`working_in`', 'w' => '=', 't' => 'int'), '_age' => array('c' => '`s`.`_age`', 'w' => '=', 't' => 'int'), '_lang' => array('c' => '`a`.`_lang`', 'w' => '=', 't' => 'str'), '_subject' => array('c' => '`s`.`_subjects`', 'w' => '=', 't' => 'str'), 'tape' => array('c' => '`a`.`tape`', 'w' => '=', 't' => 'int'), 'followers_ot' => array('c' => '`a`.`followers`', 'w' => '>=', 't' => 'int'), 'followers_do' => array('c' => '`a`.`followers`', 'w' => '<=', 't' => 'int'), 'allow_bonus' => array('c' => '`s`.`_allow_bonus_pay`', 'w' => '=', 't' => 'enum'), 'allow_adalt' => array('c' => 'FIND_IN_SET(1, `s`.`_subjects`)', 'w' => '=', 't' => 'sql'), 'allow_profanity' => array('c' => 'FIND_IN_SET(2, `s`.`_subjects`)', 'w' => '=', 't' => 'sql'), 'allow_profanity' => array('c' => 'FIND_IN_SET(2, `s`.`_subjects`)', 'w' => '=', 't' => 'sql'), 'show_only_white_list' => array('c' => 'EXISTS (SELECT `id` FROM {{tw_black_white_list}} WHERE tw_id = `a`.`id` AND _type=1 AND owner_id=' . Yii::app()->user->id . ')', 'w' => '=', 't' => 'sql'), 'no_show_block_list' => array('c' => 'NOT EXISTS (SELECT `id` FROM {{tw_black_white_list}} WHERE tw_id = `a`.`id` AND _type=0 AND owner_id=' . Yii::app()->user->id . ')', 'w' => '=', 't' => 'sql'),);

		$oR = (isset($_GET['_o']) AND array_key_exists($_GET['_o'], $ordArr)) ? $ordArr[$_GET['_o']] : $ordArr['date'];
		$oT = (isset($_GET['_a']) AND ($_GET['_a'] == 'ASC' or $_GET['_a'] == 'DESC')) ? $_GET['_a'] : "DESC";
		$q  = (isset($_GET['_q']) AND trim($_GET['_q']) != '') ? $_GET['_q'] : false;

		$prm = array();

		if(isset($_GET['limit']) && intval($_GET['limit']) AND intval($_GET['limit']) <= 50) {
			$limit                                 = intval($_GET['limit']);
			Yii::app()->session['_accountsTLimit'] = intval($_GET['limit']);
		}
		else if(isset(Yii::app()->session['_accountsTLimit']) AND intval(Yii::app()->session['_accountsTLimit']) AND intval(Yii::app()->session['_accountsTLimit']) <= 50) {
			$limit = intval(Yii::app()->session['_accountsTLimit']);
		}
		else
			$limit = 10;

		$sql = "SELECT `a`.`id`, `a`.`screen_name`, `a`.`name`, `a`.`avatar`, `a`.`date_add`, `a`.`itr`,`a`.`followers`, `a`.`white_list`, `a`.`black_list`, `a`.`_posts_count`, `s`.`_price`,`a`.`tape`,`a`.`in_yandex` FROM {{tw_accounts}} `a` LEFT JOIN {{tw_accounts_settings}} `s` ON `a`.`id`=`s`.`tid`";

		$_list = Yii::app()->db->createCommand("SELECT * FROM {{tw_black_white_list}} WHERE owner_id=:id");
		$_list->bindParam(':id', $uid, PDO::PARAM_INT);
		$_listRead = $_list->queryAll();

		$accounts_count_in_whitelist = 0;
		$accounts_count_in_blacklist = 0;
		$wids                        = array();
		$bids                        = array();

		foreach($_listRead as $k => $v) {
			if($v['_type'] == 1) {
				$wids[] = $v['tw_id'];
				$accounts_count_in_whitelist++;
			}
			else {
				$bids[] = $v['tw_id'];
				$accounts_count_in_blacklist++;
			}
		}

		if(isset($_POST['_tlist']) AND ($_POST['_tlist'] == 'black' OR $_POST['_tlist'] == 'white')) {
			if($_POST['_tlist'] == 'white') {
				$crt[] = "id IN('" . implode("', '", $wids) . "')";
			}
			else
				$crt[] = "id IN('" . implode("', '", $bids) . "')";
		}
		else if(isset($_POST['Params']) AND count($_POST['Params'])) {

			$prmSet = THelper::setParams($_POST['Params'], $params);

			foreach($prmSet['crt'] as $k => $v) {
				$crt[] = $v;
			}
			foreach($prmSet['prm'] as $_k => $_v) {
				$prm[] = $_v;
			}
		}

		if($q) {
			$crt[] = "(`a`.`screen_name` LIKE :screen_name OR `a`.`name` LIKE :name)";
			$prm[] = array('screen_name', '%' . $q . '%');
			$prm[] = array('name', '%' . $q . '%');
		}

		$crt[] = "_group=0";

		$csql = "SELECT COUNT(*) as count FROM {{tw_accounts}} `a` LEFT JOIN {{tw_accounts_settings}} `s` ON `a`.`id`=`s`.`tid`";

		if(count($crt)) {
			$sql .= " WHERE " . implode(" AND ", $crt);
			$csql .= " WHERE " . implode(" AND ", $crt);
		}

		$s_count = Yii::app()->db->createCommand($csql);

		foreach($prm as $cel) {
			if($cel[0] == 'int') $s_count->bindParam(':' . $cel[0], $cel[1], PDO::PARAM_INT);
			else
				$s_count->bindParam(':' . $cel[0], $cel[1], PDO::PARAM_STR);
		}

		$_count = $s_count->queryScalar();

		$sql .= " ORDER BY " . $oR['o'] . " " . $oT;

		if($limit) {
			$pages = new CPagination($_count);

			$pages->pageSize = $limit;
			$sql .= " LIMIT :offset,:limit";
			$plimit = ($pages->currentPage * $pages->pageSize);
		}

		$query = Yii::app()->db->createCommand($sql);

		foreach($prm as $el) {
			if($el[0] == 'int') $query->bindParam(':' . $el[0], $el[1], PDO::PARAM_INT);
			else
				$query->bindParam(':' . $el[0], $el[1], PDO::PARAM_STR);
		}

		if($limit) {
			$query->bindParam(':offset', $plimit, PDO::PARAM_INT);
			$query->bindParam(':limit', $limit, PDO::PARAM_INT);
		}

		$model = $query->queryAll();

		if(Yii::app()->request->isAjaxRequest) {
			echo json_encode(array('stats' => $this->renderPartial('_stats', array('accounts_count_in_whitelist' => $accounts_count_in_whitelist, '_count' => $_count, 'accounts_count_in_blacklist' => $accounts_count_in_blacklist), true), 'html' => $this->renderPartial('_list', array('wids' => $wids, 'bids' => $bids, 'list' => $model), true), 'pages' => $this->renderPartial('_pages', array('pages' => $pages), true)));
		}
		else {
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/www-twitter-zTrE2z.js', CClientScript::POS_END);

			$ageData  = require Yii::app()->getModulePath() . '/twitter/data/_age.php';
			$subjects = Html::groupByKey(Subjects::model()->findALl(array('order' => 'sort')), 'id', '_key', 'parrent');

			$this->render('index', ['wids' => $wids, 'bids' => $bids, 'accounts_count_in_whitelist' => $accounts_count_in_whitelist, 'accounts_count_in_blacklist' => $accounts_count_in_blacklist, '_count' => $_count, 'pages' => $pages, 'model' => $model, 'ageData' => $ageData, 'subjects' => $subjects, 'limitList' => $this->limitList]);
		}
	}

}
