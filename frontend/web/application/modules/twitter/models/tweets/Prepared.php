<?php

namespace twitter\models\tweets;

use Yii;
use twitter\components\Downloads;

class Prepared extends \FormModel
{
	public $_action;
	public $_rosterCount;
	public $_tid;

	protected $_actions = [
		'download' => ['method' => 'tweetsDownalod', 'render' => false],
		'remove' => ['method' => 'removeList', 'render' => true]
	];
	protected $tweets;
	protected $_pages;
	protected $_limit = 10;
	protected $_message = '';
	protected $_roster;
	protected $_list;

	public function rules()
	{
		return [
			['_action', 'ext.validators.actionsValidator', 'allowEmpty' => true, 'actions' => $this->_actions],
			['_tid', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15, 'on' => 'remove,download'],
		];
	}

	protected function afterValidate()
	{
		if(!\CHelper::isEmpty($this->_action))
			$this->_init();
	}

	protected function _init()
	{
		$method = is_array($this->_action) && isset($this->_action['method']) ? $this->_action['method'] : $this->_action;

		if(method_exists($this, $method))
			$this->$method();
		else
			$this->addError('_action', Yii::t('yii', 'Your request is invalid.'));
	}

	public function getRoster()
	{
		if($this->_roster === NULL) {
			$this->_roster = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsLists}} WHERE owner_id=:owner AND _hash=:key")->queryRow(true, [':key' => $this->_tid, ':owner' => Yii::app()->user->id]);
		}

		if($this->_roster === false)
			$this->setCode(404)->addError('id', 'Выбранный вами список не найден.');
		else
			return $this->_roster;
	}

	public function rosterCount()
	{
		if($this->_rosterCount === NULL)
			$this->_rosterCount = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsLists}} WHERE owner_id=:owner")->queryScalar(array(':owner' => Yii::app()->user->id));

		return $this->_rosterCount;
	}

	public function getTweets()
	{
		if($this->tweets === NULL) {
			$this->tweets = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsRoster}} WHERE _key=:key AND owner_id=:owner")->queryAll(true, [':key' => $this->_tid, ':owner' => Yii::app()->user->id]);
		}

		return $this->tweets;
	}

	public function getList()
	{
		if($this->_list === NULL) {
			$this->_list = Yii::app()->db->createCommand("SELECT r.*, (SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE _key=r._hash) as _count FROM {{twitter_tweetsLists}} r WHERE owner_id=:owner")->queryAll(true, [':owner' => Yii::app()->user->id]);
		}

		return $this->_list;
	}

	public function getPages()
	{
		if($this->_pages === NULL) {
			$this->_pages           = new \CPagination($this->rosterCount());
			$this->_pages->pageSize = $this->_limit;
		}

		return $this->_pages;
	}

	public function getView()
	{
		if((isset($this->_action['render']) && $this->_action['render'] === true) || \CHelper::isEmpty($this->_action))
			return true;
		else
			return false;
	}

	public function getViewFile($partial = false)
	{
		if(!\CHelper::isEmpty($this->_action) && isset($this->_action['renderFiles'])) {
			if(isset($this->_action['renderFiles']))
				return $partial === true ? $this->_action['renderFiles']['rows'] : $this->_action['renderFiles']['index'];
			else
				throw new \CException(Yii::t('yii', 'Render file not found.'));
		}
		else
			return $partial === true ? '_preparedRows' : '_prepared';
	}

	protected function removeList()
	{
		$roster = $this->getRoster();
		if(Yii::app()->db->createCommand("DELETE FROM {{twitter_tweetsLists}} WHERE _hash=:id AND owner_id=:owner")->execute([':id' => $this->_tid, ':owner' => Yii::app()->user->id])) {
			$this->_message = 'Список "' . \Html::encode($this->getRoster()['title']) . '" успешно удален.';
			Yii::app()->db->createCommand("DELETE FROM {{twitter_tweetsListsRows}} WHERE _hash=:tid")->execute([':tid' => $roster['_hash']]);
		}
		else {
			$this->_message = 'Не удалось удалить список "' . \Html::encode($this->getRoster()['title']) . '".';
		}
	}

	public function getMessage()
	{
		return $this->_message;
	}

	protected function tweetsDownalod()
	{
		$download = new Downloads;

		$download->setTweets($this->getTweets());
		$download->setOutPutFile('txt');
		$download->setFileName(\Html::encode($this->getRoster()['title']) . '-' . $this->getRoster()['_hash']);
		$download->outPutFile();
	}
}
