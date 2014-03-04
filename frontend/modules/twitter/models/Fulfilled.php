<?php

class Fulfilled extends FormModel
{

	public $pages;
	public $order;
	public $sort = "DESC";
	public $tid;
	protected $_count;
	protected $oArr = array('date' => 'tw._date', 'price' => 'tw._cost');

	public function rules()
	{
		return array(
			array('tid', 'numerical', 'integerOnly' => true),
			array('order', 'in', 'range' => array('date', 'price')),
			array('sort', 'in', 'range' => array('DESC', 'ASC')),
		);
	}

	public function _getRows($o = '', $s = '')
	{
		$this->pages           = new CPagination($this->_getCount());
		$this->pages->pageSize = 25;

		if(isset($this->oArr[$this->order])) {
			$order = $this->oArr[$this->order] . ' ' . $this->sort;
		}
		else
			$order = $this->oArr['date'] . ' DESC';

		$w = array('tw.owner_id=:owner');
		$p = array(':owner' => Yii::app()->user->id);

		if($this->tid) {
			$w[]       = "tw.tid=:tid";
			$p[':tid'] = $this->tid;
		}

		$where = "WHERE " . implode(" AND ", $w);

		$rows = Yii::app()->db->createCommand("SELECT tw.tw_id,tw._text,tw._date,tw._cost,tw.pay_type,a.screen_name,a.avatar,a.name FROM {{tw_tweets}} tw INNER JOIN {{tw_accounts}} a ON tw.tid=a.id {$where}  ORDER BY {$order} LIMIT " . $this->pages->getOffset() . "," . $this->pages->getLimit())->queryAll(true, $p);

		return $rows;
	}

	public function _getCount()
	{
		if($this->_count === NULL) {
			$w = array('owner_id=:owner');
			$p = array(':owner' => Yii::app()->user->id);

			if($this->tid) {
				$w[]       = "tid=:tid";
				$p[':tid'] = $this->tid;
			}

			$where = "WHERE " . implode(" AND ", $w);

			$this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_tweets}} {$where}")->queryScalar($p);
		}

		return $this->_count;
	}

}
