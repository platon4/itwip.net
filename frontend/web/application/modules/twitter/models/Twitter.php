<?php

namespace twitter\models;

use Yii;

class Twitter extends \FormModel
{
	public $pageLimits = [
		10 => ['title' => '10', 'value' => '10'],
		20 => ['title' => '20', 'value' => '20'],
		30 => ['title' => '30', 'value' => '30'],
		40 => ['title' => '40', 'value' => '40'],
		50 => ['title' => '50', 'value' => '50']
	];
	public $id;
	public $ot_itr = 1;
	public $do_itr = 100;
	public $price_post_ot = 1;
	public $price_post_do = 10000;
	public $ya_r_ot = 0;
	public $ya_r_do = 5000000;
	public $googl_rang_ot = 0;
	public $googl_rang_do = 10;
	public $_age;
	public $blogging_topics = [];
	public $age_blog_ot = 1;
	public $age_blog_do = 90;
	public $gender = 0;
	public $in_yandex = 'matter';
	public $in_google = 0;
	public $language_blog = 'matter';
	public $added_system = 'all';
	public $pay_method = 0;
	public $pType = ['manual' => 1, 'auto' => 1];
	public $show_only_white_list;
	public $not_black_list;
	public $bw;

	public $limit = 10;

	public $_a = 'DESC';
	public $_o = 'date';

	public $tape;
	public $followers_ot = 500;
	public $followers_do = 5000000;

	protected $_ages;
	protected $_subjects;
	protected $_count;
	protected $_bwlist;
	protected $_stats;

	/**
	 * @var объект класса \CPagination
	 */
	protected $_pages;

	protected $filters = [];
	protected $_orders = [
		'date' => '`tw`.`date_add`',
		'itr' => '`tw`.`itr`',
		'wlist' => '`tw`.`white_list`',
		'blist' => '`tw`.`black_list`',
		'cpost' => '`tw`.`_posts_count`',
		'price' => '`st`.`_price`',
		'tape' => '`tw`.`tape`',
		'followers' => '`tw`.`followers`',
		'group' => 'tw._group'
	];

	protected $pTypes;

	protected $_where;

	public function rules()
	{
		return [
			['limit', 'in', 'range' => array_keys($this->pageLimits)],
			['added_system', 'in', 'range' => ['all', 'today', 'three_days', 'seven_days', 'month']],
			['age_blog_ot,_age_blog_do,show_only_white_list,not_black_list,age,blogging_topics,tape,_age', 'safe'],

			['language_blog', 'in', 'range' => ['matter', 'ru', 'en']],

			['_a', 'in', 'range' => ['DESC', 'ASC'], 'allowEmpty' => false],
			['_o', 'in', 'range' => array_keys($this->_orders), 'allowEmpty' => false],

			/*
			 * iTr
			 */
			['ot_itr,do_itr', 'numerical', 'min' => 1, 'max' => 100],
			['ot_itr', 'compare', 'compareAttribute' => 'do_itr', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_itr')],


			/*
			 * Цена твита
			 */
			['price_post_ot,price_post_do', 'numerical', 'min' => 1, 'max' => 10000],
			['price_post_ot', 'compare', 'compareAttribute' => 'price_post_do', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_price_post')],

			['pType', 'ConfirmTypeValidate'],

			['pay_method', 'in', 'range' => [0, 1], 'message' => Yii::t('twitterModule.tweets', '_no_pay_system')],

			/*
			 * Yandex
			 */
			['in_yandex', 'in', 'range' => ['matter', 'yes', 'no']],
			['ya_r_ot', 'compare', 'compareAttribute' => 'ya_r_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_ya_r')],

			/*
			 * Google
			 */
			['googl_rang_ot,googl_rang_do', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 10],
			['googl_rang_ot', 'compare', 'compareAttribute' => 'googl_rang_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_googl_rang')],

			/*
			 * Возраст
			 */
			['age_blog_ot', 'compare', 'compareAttribute' => 'age_blog_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_age_blog')],

			['id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false, 'message' => 'Неправильно указан идентификатор запроса.', 'on' => 'bw'],
			['bw', 'in', 'range' => ['black', 'white'], 'message' => 'Не удалось обработать запрос, пожалуйста попробуйте еще раз.', 'on' => 'bw'],

			/*
			 * Читатели
			 */
			['followers_ot,followers_do', 'numerical', 'integerOnly' => true, 'min' => 500, 'max' => 99999999999, 'on' => 'get,rows'],
			['followers_ot', 'compare', 'compareAttribute' => 'followers_do', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_followers')],

			['gender', 'in', 'range' => [0, 1, 2]],
		];
	}

	/**
	 * @return array
	 */
	public function attributeLabels()
	{
		return [
			'in_yandex' => Yii::t('twitterModule.tweets', '_in_yandex'),
			'language_blog' => Yii::t('twitterModule.tweets', '_language_blog'),
			'added_system' => Yii::t('twitterModule.tweets', '_added_system'),
			'age_blog_ot' => Yii::t('twitterModule.tweets', '_age_blog'),
			'gender' => Yii::t('twitterModule.tweets', '_floor_blogger'),
			'ot_itr' => 'iTr "от"',
			'do_itr' => 'iTr "до"',
			'price_post_ot' => 'Цена твита "от"',
			'price_post_do' => 'Цена твита "до"',
			'ya_r_ot' => 'Яндекс авторитет "от"',
			'ya_r_do' => 'Яндекс авторитет "до"',
			'googl_rang_ot' => 'Google PR "от"',
			'googl_rang_do' => 'Google PR "до"',
			'followers_ot' => 'Читателей "от"',
			'followers_do' => 'Читателей "до"',
			'_ping' => 'Weblogs.Ping',
			'interval' => 'Интервал',
			'_q' => 'Поиск твита',
			'_o' => 'Параметр сортировки',
			'_a' => 'Параметр сортировки',
			'sDate' => 'размещать с'
		];
	}

	public function init()
	{
		$this->age_blog_do = (int) round(((time() - strtotime('15.07.2006 00:00:00')) / 86400) / 31);
	}

	/*
	 * Проверка способа размещение
	 */
	public function ConfirmTypeValidate()
	{
		$type = [];
		if((!isset($this->pType['auto']) || $this->pType['auto'] == 0) && (!isset($this->pType['manual']) || $this->pType['manual'] == 0))
			$this->addError('confirm', Yii::t('twitterModule.tweets', '_no_sustem_confirm'));
		else {
			if(isset($this->pType['manual']) && $this->pType['manual'] == 1)
				$type[] = 0;

			if(isset($this->pType['auto']) && $this->pType['auto'] == 1)
				$type[] = 1;
		}

		$this->pTypes = $type;
	}

	public function getStat()
	{
		if($this->_stats === NULL)
			$this->_stats = Yii::app()->db->createCommand("SELECT black_list, white_list FROM {{tw_accounts}} WHERE id=:id")->queryRow(true, [':id' => $this->id]);

		return $this->_stats;
	}

	public function getRows()
	{
		$accountsRows = Yii::app()->db->createCommand("SELECT `tw`.`id`, `tw`.`screen_name`, `tw`.`name`, `tw`.`avatar`, `tw`.`date_add`, `tw`.`itr`,`tw`.`followers`, `tw`.`white_list`, `tw`.`black_list`, `tw`.`_posts_count`, `st`.`_price`,`tw`.`tape`,`tw`.`in_yandex` FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id
                                                                                            " . $this->where()['params'] . " ORDER BY " . $this->_orders[$this->_o] . ' ' . $this->_a . " LIMIT " . $this->getPages()->getOffset() . ", " . $this->getPages()->getLimit())
			->queryAll(true, $this->where()['values']);

		$in_bwList = Yii::app()->user->getBWList();
		$rows      = [];

		foreach($accountsRows as $k => $row) {
			$rows[] = [
				'id' => $row['id'],
				'screen_name' => $row['screen_name'],
				'name' => $row['name'],
				'avatar' => $row['avatar'],
				'date_add' => $row['date_add'],
				'itr' => $row['itr'],
				'followers' => $row['followers'],
				'tape' => $row['tape'],
				'in_yandex' => $row['in_yandex'],
				'inBlackList' => 0,
				'inWhiteList' => 0,
				'_price' => $row['_price'],
				'black_list' => $row['black_list'],
				'white_list' => $row['white_list']
			];

			if(in_array($row['id'], $in_bwList['black']))
				$rows[$k]['inBlackList'] = 1;
			elseif(in_array($row['id'], $in_bwList['white']))
				$rows[$k]['inWhiteList'] = 1;
		}

		return $rows;
	}

	protected function where()
	{
		if($this->_where === NULL) {
			$fileds = ['`tw`.`_status`=\'1\''];

			if($this->pay_method == 0)
				$fileds[] = '`tw`.`_group`=\'0\'';

			$values = [];

			$params = [
				'ot_itr' => ['c' => '`tw`.`itr`', 'w' => '>=', 't' => 'int'], //itr начало
				'do_itr' => ['c' => '`tw`.`itr`', 'w' => '<=', 't' => 'int'], //itr конец
				'followers_ot' => ['c' => '`tw`.`followers`', 'w' => '>=', 't' => 'int'], //читатели начало
				'followers_do' => ['c' => '`tw`.`followers`', 'w' => '<=', 't' => 'int'], //читатели конец
				'price_post_ot' => ['c' => '`st`.`_price`', 'w' => '>=', 't' => 'int'],
				'price_post_do' => ['c' => '`st`.`_price`', 'w' => '<=', 't' => 'int'],
				'ya_r_ot' => ['c' => '`tw`.`yandex_rank`', 'w' => '>=', 't' => 'int'],
				'ya_r_do' => ['c' => '`tw`.`yandex_rank`', 'w' => '<=', 't' => 'int'],
				'googl_rang_ot' => ['c' => '`tw`.`google_pr`', 'w' => '>=', 't' => 'int'],
				'googl_rang_do' => ['c' => '`tw`.`google_pr`', 'w' => '<=', 't' => 'int'],
				'age_blog_ot' => ['c' => '`tw`.`created_at`', 'w' => '<=', 't' => 'days'],
				'age_blog_do' => ['c' => '`tw`.`created_at`', 'w' => '>=', 't' => 'days'],
				'blogging_topics' => ['c' => '`st`.`_subjects`', 'w' => '', 't' => 'in'],
				'_age' => ['c' => '`st`.`_age`', 'w' => '=', 't' => 'int'],
				'gender' => ['c' => '`st`.`_gender`', 'w' => '=', 't' => 'int'],
				'in_yandex' => ['c' => '`tw`.`in_yandex`', 'w' => '=', 't' => 'enum'],
				'language_blog' => ['c' => '`tw`.`_lang`', 'w' => '=', 't' => 'value'],
				'added_system' => ['c' => '`tw`.`date_add`', 'w' => '>=', 't' => 'days'],
				'pTypes' => ['c' => '`st`.`working_in`', 'w' => '', 't' => 'in'],
				'pay_method' => ['c' => '`st`.`_allow_bonus_pay`', 'w' => '0', 't' => 'not'],
				'tape' => ['c' => '`tw`.`tape`', 'w' => '=', 't' => 'int'],

				/*
				 * Черный-Белый список
				 */
				'show_only_white_list' => ['c' => 'EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE owner_id=' . \Yii::app()->user->id . ' AND tw_id = `tw`.`id` AND _type=1)', 'w' => '=', 't' => 'sql'],
				'not_black_list' => ['c' => 'NOT EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE owner_id=' . \Yii::app()->user->id . ' AND tw_id = `tw`.`id` AND _type=0)', 'w' => '=', 't' => 'sql'],
			];

			if(isset($this->blogging_topics[0]) && $this->blogging_topics[0] == 0)
				$this->blogging_topics = [];

			if($this->show_only_white_list)
				unset($params['not_black_list']);

			/*
			 * Форматируем запрос к базе
			 * array
			 */
			foreach(\THelper::setParams(array_merge($this->attributes, ['pTypes'=>$this->pTypes]), $params) as $rows) {
				if(isset($rows['fields']))
					$fileds[] = $rows['fields'];

				if(isset($rows['values']))
					$values[':' . $rows['values'][0]] = $rows['values'][1];
			}

			$this->_where = ['params' => ' WHERE ' . implode(' AND ', $fileds), 'values' => $values];
		}

		return $this->_where;
	}

	public function getPages()
	{
		if($this->_pages === NULL) {
			$this->_pages           = new \CPagination($this->getCount());
			$this->_pages->pageSize = $this->getLimit();
		}

		return $this->_pages;
	}

	public function getCount()
	{
		if($this->_count === NULL) {
			$this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id" . $this->where()['params'] . "")->queryScalar($this->where()['values']);
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
			$rows = Yii::app()->db->createCommand("SELECT id, _type FROM {{twitter_bwList}} WHERE owner_id=:owner")->queryAll(true, [':owner' => Yii::app()->user->id]);

			$black = [];
			$white = [];

			if($rows !== false) {
				foreach($rows as $row) {
					if($row['_type'] == 0)
						$black[] = $row['id'];
					else
						$white[] = $row['id'];
				}
			}

			$this->_bwlist = ['black' => $black, 'white' => $white];
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

	public function bwToggle()
	{
		$type   = $this->bw == 'white' ? 1 : 0;
		$status = Yii::app()->db->createCommand("SELECT _type FROM {{twitter_bwList}} WHERE owner_id=:owner AND tw_id=:id")->queryRow(true, [':owner' => Yii::app()->user->id, ':id' => $this->id]);

		if($status === false || $status['_type'] != $type) {
			if(Yii::app()->db->createCommand("INSERT INTO {{twitter_bwList}} (owner_id, tw_id, _type, _date) VALUES (:owner,:id,:type,:date) ON DUPLICATE KEY UPDATE _type=:type,_date=:date")->execute([':owner' => Yii::app()->user->id, ':id' => $this->id, ':type' => $type, ':date' => date('Y-m-d H:i:s')])) {
				Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET " . ($type == 1 ? 'white_list=white_list+1' : 'black_list=black_list+1') . " WHERE id=:id")->execute([':id' => $this->id]);

				if($status !== false)
					Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET " . ($type == 0 ? 'white_list=white_list-1' : 'black_list=black_list-1') . " WHERE id=:id")->execute([':id' => $this->id]);
			}
		}
		else {
			if(Yii::app()->db->createCommand("DELETE FROM {{twitter_bwList}} WHERE owner_id=:owner AND tw_id=:id")->execute([':owner' => Yii::app()->user->id, ':id' => $this->id]))
				Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET " . ($type == 1 ? 'white_list=white_list-1' : 'black_list=black_list-1') . " WHERE id=:id")->execute([':id' => $this->id]);
		}

		return true;
	}
} 