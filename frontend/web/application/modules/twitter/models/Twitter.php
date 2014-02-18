<?php

namespace twitter\models;

class Twitter extends \FormModel
{
	public $pageLimits = [
		10 => ['title' => '10', 'value' => '10'],
		20 => ['title' => '20', 'value' => '20'],
		30 => ['title' => '30', 'value' => '30'],
		40 => ['title' => '40', 'value' => '40'],
		50 => ['title' => '50', 'value' => '50']
	];

	public $limit = 10;

	protected $_ages;
	protected $_subjects;
	protected $_count;
	protected $_bwlist;
	protected $_pages;
	protected $sortFields = [];

	public function rules()
	{
		return [
			['limit', 'in', 'range' => array_keys($this->pageLimits)]
		];
	}

	public function getRows()
	{

		return [];
		$crt    = array('`a`.`_status`=\'1\'');
		$ordArr = array('date' => array('o' => '`a`.`date_add`'), 'itr' => array('o' => '`a`.`itr`'), 'wlist' => array('o' => '`a`.`white_list`'), 'blist' => array('o' => '`a`.`black_list`'), 'cpost' => array('o' => '`a`.`_posts_count`'), 'price' => array('o' => '`s`.`_price`'), 'tape' => array('o' => '`a`.`tape`'), 'followers' => array('o' => '`a`.`followers`'),);

		$params = array('itr_ot' => array('c' => '`a`.`itr`', 'w' => '>=', 't' => 'int'), 'itr_do' => array('c' => '`a`.`itr`', 'w' => '<=', 't' => 'int'), 'glp_ot' => array('c' => '`a`.`google_pr`', 'w' => '>=', 't' => 'int'), 'glp_do' => array('c' => '`a`.`google_pr`', 'w' => '<=', 't' => 'int'), 'yav_ot' => array('c' => '`a`.`yandex_rank`', 'w' => '>=', 't' => 'int'), 'yav_do' => array('c' => '`a`.`yandex_rank`', 'w' => '<=', 't' => 'int'), 'in_google' => array('c' => '`a`.`in_google`', 'w' => '=', 't' => 'enum'), 'in_yandex' => array('c' => '`a`.`in_yandex`', 'w' => '=', 't' => 'enum'), 'post_price_ot' => array('c' => '`s`.`_price`', 'w' => '>=', 't' => 'int'), 'post_price_do' => array('c' => '`s`.`_price`', 'w' => '<=', 't' => 'int'), 'time_add' => array('c' => '`a`.`date_add`', 'w' => '>=', 't' => 'days'), 'age_ot' => array('c' => '`a`.`created_at`', 'w' => '<=', 't' => 'days'), 'age_do' => array('c' => '`a`.`created_at`', 'w' => '>=', 't' => 'days'), '_gender' => array('c' => '`s`.`_gender`', 'w' => '=', 't' => 'int'), 'pType' => array('c' => '`s`.`working_in`', 'w' => '=', 't' => 'int'), '_age' => array('c' => '`s`.`_age`', 'w' => '=', 't' => 'int'), '_lang' => array('c' => '`a`.`_lang`', 'w' => '=', 't' => 'str'), '_subject' => array('c' => '`s`.`_subjects`', 'w' => '=', 't' => 'str'), 'tape' => array('c' => '`a`.`tape`', 'w' => '=', 't' => 'int'), 'followers_ot' => array('c' => '`a`.`followers`', 'w' => '>=', 't' => 'int'), 'followers_do' => array('c' => '`a`.`followers`', 'w' => '<=', 't' => 'int'), 'allow_bonus' => array('c' => '`s`.`_allow_bonus_pay`', 'w' => '=', 't' => 'enum'), 'allow_adalt' => array('c' => 'FIND_IN_SET(1, `s`.`_subjects`)', 'w' => '=', 't' => 'sql'), 'allow_profanity' => array('c' => 'FIND_IN_SET(2, `s`.`_subjects`)', 'w' => '=', 't' => 'sql'), 'allow_profanity' => array('c' => 'FIND_IN_SET(2, `s`.`_subjects`)', 'w' => '=', 't' => 'sql'), 'show_only_white_list' => array('c' => 'EXISTS (SELECT `id` FROM {{tw_black_white_list}} WHERE tw_id = `a`.`id` AND _type=1 AND owner_id=' . Yii::app()->user->id . ')', 'w' => '=', 't' => 'sql'), 'no_show_block_list' => array('c' => 'NOT EXISTS (SELECT `id` FROM {{tw_black_white_list}} WHERE tw_id = `a`.`id` AND _type=0 AND owner_id=' . Yii::app()->user->id . ')', 'w' => '=', 't' => 'sql'),);


		$prm = array();

		$sql = "SELECT `a`.`id`, `a`.`screen_name`, `a`.`name`, `a`.`avatar`, `a`.`date_add`, `a`.`itr`,`a`.`followers`, `a`.`white_list`, `a`.`black_list`, `a`.`_posts_count`, `s`.`_price`,`a`.`tape`,`a`.`in_yandex` FROM {{tw_accounts}} `a` LEFT JOIN {{tw_accounts_settings}} `s` ON `a`.`id`=`s`.`tid`";

		$_list = Yii::app()->db->createCommand("SELECT * FROM {{tw_black_white_list}} WHERE owner_id=:id");
		$_list->bindParam(':id', $uid, PDO::PARAM_INT);
		$_listRead = $_list->queryAll();

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

		if(count($crt)) {
			$sql .= " WHERE " . implode(" AND ", $crt);

		}

		$sql .= " ORDER BY " . $oR['o'] . " " . $oT;

		foreach($prm as $el) {
			if($el[0] == 'int') $query->bindParam(':' . $el[0], $el[1], PDO::PARAM_INT);
			else
				$query->bindParam(':' . $el[0], $el[1], PDO::PARAM_STR);
		}


		$model = $query->queryAll();
	}

	protected function where()
	{
		return [
			'count' => ['params' => '', 'values' => []],
			'rows' => ['params' => '', 'values' => []]
		];
	}

	public function getPages()
	{
		if($this->_pages === NULL) {

		}

		return $this->_pages;
	}

	public function getCount()
	{
		if($this->_count === NULL) {
			$this->_count = \Yii::app()->db->createCommand("SELECT COUNT(*) as count FROM {{tw_accounts}} `a` LEFT JOIN {{tw_accounts_settings}} `s` ON `a`.`id`=`s`.`tid` " . $this->where()['count']['params'])->queryScalar($this->where()['count']['values']);
		}

		return $this->_count;
	}

	public function getLimit()
	{
		return $this->limit;
	}

	public function getPageLimits()
	{
		return $this->pageLimits;
	}

	public function bwList()
	{
		if($this->_bwlist === NULL) {
			$this->_bwlist = ['black' => 0, 'white' => 0];
		}

		return $this->_bwlist;
	}

	/*
	 * @return array
	 */
	public function getAges()
	{
		if($this->_ages === NULL)
			$this->_ages = require \Yii::app()->getModulePath() . '/twitter/data/_age.php';

		return $this->_ages;
	}

	/*
	 * @return array
	 */
	public function getSubjects()
	{
		if($this->_subjects === NULL)
			$this->_subjects = \Html::groupByKey(\Subjects::model()->findALl(array('order' => 'sort')), 'id', '_key', 'parrent');

		return $this->_subjects;
	}
} 