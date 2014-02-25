<?php

class WebUser extends CWebUser
{
	private $_model;
	private $_settings;
	private $_loyalt;
	protected $_bwlist;

	public $identityCookie = array(
		'path' => '/',
		'domain' => '.itwip.net',
	);

	public function init()
	{
		$conf                 = Yii::app()->session->cookieParams;
		$this->identityCookie = array(
			'path' => $conf['path'],
			'domain' => $conf['domain'],
		);

        $this->setStateKeyPrefix('aeca95a0e4be8d86279a0841a65bc4b7');
		parent::init();
	}

	public function getName()
	{
		$user = $this->loadUser();

		return $user->name;
	}

	public function _get($key)
	{
		$user = $this->loadUser();

		if($user) {
			if(isset($user->$key)) {
				return $user->{$key};
			}
			else {
				throw new CHttpException(500, Yii::t('main', '_missing_user_data', array(
					'{key}' => $key)));
			}
		}
		else {
			Yii::app()->user->logout();

			if(Yii::app()->request->isAjaxRequest) {
				echo "Access diented.";
				Yii::app()->end();
			}
			else {
				Yii::app()->request->redirect(Yii::app()->homeUrl);
			}
		}
	}

	public function getBWList()
	{
		if($this->_bwlist === NULL) {
			$bw = Yii::app()->db->createCommand("SELECT id,tw_id,_type FROM {{twitter_bwList}} WHERE owner_id=:owner")->queryAll(true, [':owner' => Yii::app()->user->id]);
			$white = [];
			$black = [];

			foreach($bw as $row) {
				if($row['_type'] == 1)
					$white[] = $row['tw_id'];
				elseif($row['_type'] == 0)
					$black[] = $row['tw_id'];
			}

			$this->_bwlist = ['white' => $white, 'black' => $black];
		}

		return $this->_bwlist;
	}

	public function _getLoyalt($key, $id = 0)
	{
		if(!$id)
			$id = Yii::app()->user->id;

		if($this->_loyalt === NULL) {
			$this->_loyalt = Yii::app()->db->createCommand("SELECT * FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true, array(
				':id' => $id));
		}

		return isset($this->_loyalt[$key]) ? $this->_loyalt[$key] : '';
	}

	public function getSettings($id = 0)
	{
		if($this->_settings === NULL) {
			$this->_settings = @unserialize($this->loadUser($id)->_settings);
		}

		return $this->_settings;
	}

	public function _setting($key, $id = 0)
	{
		$settings = $this->getSettings($id);

		return isset($settings[$key]) ? $settings[$key] : false;
	}

	public function _getBalance()
	{
		$user = $this->loadUser();

		return round($user->money_amount + $user->bonus_money, 2);
	}

	function getRole()
	{
		if($user = $this->loadUser()) {
			// в таблице User есть поле role
			return $user->role;
		}
	}

	// Load user model.
	private function loadUser($id = 0)
	{
		if($this->_model === NULL) {
			if(!Yii::app()->user->isGuest)
				$this->_model = User::model()->findByPk(Yii::app()->user->id);
			elseif($id)
				$this->_model = User::model()->findByPk($id);
		}

		return $this->_model;
	}

	public function getReturnUrl($defaultUrl = NULL)
	{

		if($defaultUrl === NULL) {
			$defaultReturnUrl = Yii::app()->getUrlManager()->showScriptName ? Yii::app()->getRequest()->getScriptUrl() : Yii::app()->getRequest()->getBaseUrl() . '/';
		}
		else {
			$defaultReturnUrl = CHtml::normalizeUrl($defaultUrl);
		}
		return $this->getState('__returnUrl', $defaultReturnUrl);
	}
}
