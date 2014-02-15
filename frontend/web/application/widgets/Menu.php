<?php

class Menu extends CWidget
{

	public $breadcrumbs = array();
	public $favMenu = array();
	public $menu = array();
	public $ajax = false;
	public $active = array();
	public $mainMenu = array();
	public $isActive = false;
	public $activeBlock = "main";
	public $addonsList;

	public function init()
	{
		$this->getMenuData();
		$this->getFavoritsList();

		if(!$this->ajax) {
			$this->render('userMenu', array('userMenu' => $this->menu['list']));
		}
		else {
			$this->render('favMenu', array('userMenu' => $this->menu['list']));
		}

		Yii::app()->session['_menu'] = $this->activeBlock;
	}

	public function setActiveMenu()
	{
		$z3q6f    = explode("/", Yii::app()->request->requestUri);
		$inActive = array();

		foreach($z3q6f as $k) {
			if(trim($k)) {
				$inActive[] = $k;
			}
		}

		if(isset($inActive[0]) AND in_array($inActive[0], $this->menu['main'])) {
			$this->isActive    = true;
			$this->activeBlock = $inActive[0];
		}
		else {
			if(!$this->isActive && isset($this->menu['main']) && isset($this->menu['active']) && count($inActive)) {
				foreach($this->menu['main'] as $_k) {
					if(isset($this->menu['active'][$_k])) {
						foreach($this->menu['active'][$_k] as $chk) {
							if($chk == $inActive[0]) {
								$this->isActive    = true;
								$this->activeBlock = $_k;
								break;
							}
						}
					}
				}
			}
		}
	}

	public function getMenu()
	{
		$this->setActiveMenu();

		$i = 0;

		foreach($this->menu['list'] as $element) {

			$i++;

			$params = array();

			if($i == 1) {
				$params[] = "style=\"border-top: none;\"";
			}

			if($this->activeBlock == $element['nactive']) {
				$params[] = "class=\"active no_remove\"";
			}

			$params[] = "data-active=\"con_m_" . $i . "\"";

			if(count($params)) {
				$imp_elm = " " . implode(" ", $params);
			}
			else {
				$imp_elm = NULL;
			}

			if($element['url'] == '/') {
				$link = '/';
			}
			else
				$link = (trim($element['url']) != "") ? Yii::app()->createUrl($element['url']) : 'javascript:;';

			echo '<li' . $imp_elm . '><a href="' . $link . '" id="m_' . $i . '"><span class="menu_icon"><i class="' . $element['icon'] . '"></i></span><span class="menu_alt"> ' . Yii::t('menu', $element['_key']) . '</span></a></li>';
		}

		return;
	}

	public function getParentMenu($data, $parent = false)
	{
		echo CHtml::openTag('ul');

		foreach($data as $element) {
			echo Html::openTag('li');

			if(trim($element['url']) != "") {
				$favActive = (in_array($element['id'], $this->favMenu)) ? " active" : "";

				if($parent)
					$angel = Html::openTag('i', array('class' => 'fa fa-caret-right')) . Html::closeTag('i');
				else
					$angel = '';

				echo Html::link($angel . Yii::t('menu', $element['_key']) . Html::openTag('span', array(
						'id' => 'fav_' . $element['id'], 'class' => 'like_menu fav-icon' . $favActive,
						'onclick' => '_addFav(\'' . $element['id'] . '\', this); return false;')) . Html::closeTag('span'), $element['url']);
			}
			else {
				echo Yii::t('menu', $element['_key']);
			}

			echo Html::closeTag('li');

			if(isset($element['smenu']) && count($element['smenu'])) {
				$this->getParentMenu($element['smenu'], true);
			}
		}

		echo CHtml::closeTag('ul');
	}

	public function getParentMenuSelect($data, $level = 0)
	{
		$level = $level + 1;

		$separator = NULL;

		for($e = 0; $e < $level; $e++) {
			$separator .= "-";
		}

		foreach($data as $element) {
			if(!in_array($element['id'], $this->favMenu)) {
				if(trim($element['url']) == "" OR !$element['parrent']) {
					$disabled = "disabled";
				}
				else {
					$disabled = "";
				}

				echo Html::openTag('option', array('disabled' => $disabled, 'value' => $element['id'])) . $separator . " " . Yii::t('menu', $element['_key']) . Html::closeTag('option');

				if(isset($element['smenu']) && count($element['smenu'])) {
					$this->getParentMenuSelect($element['smenu'], $level);
				}
			}
		}
	}

	public function getFavoritsList()
	{
		if(is_string($this->favMenu) && $this->ajax)
			$load_fav = explode(",", trim($this->favMenu));
		else
			$load_fav = explode(",", trim(Yii::app()->user->_get('favMenu')));

		$favArr = (trim($this->addonsList)) ? explode(",", trim($this->addonsList)) : $load_fav;

		foreach($favArr as $v) {
			if(intval($v)) {
				$this->favMenu[] = $v;
			}
		}
	}

	public function getFavoritsMenu()
	{
		if(count($this->favMenu)) {
			echo CHtml::openTag('ul');

			foreach($this->menu['_list'] as $element) {
				if(in_array($element['id'], $this->favMenu)) {
					echo Html::openTag('li');

					if(trim($element['url']) != "") {
						$favActive = (in_array($element['id'], $this->favMenu)) ? " active" : "";

						echo Html::link(Yii::t('menu', $element['_key']), $element['url']) . Html::openTag('span', array(
								'class' => 'like_menu fav-icon' . $favActive, 'onclick' => '_addFav(\'' . $element['id'] . '\', this); return false;')) . Html::closeTag('span');
					}
					else {
						echo Yii::t('menu', $element['_key']);
					}

					echo Html::closeTag('li');

					if(isset($element['smenu']) && count($element['smenu'])) {
						$this->getParrentMenu($element['smenu']);
					}
				}
			}

			echo CHtml::closeTag('ul');
		}
	}

	/**
	 * Protected functions
	 */
	protected function _setMenu($menu, $id = 0, $_k = false)
	{
		$list = array();
		$data = $menu;

		foreach($data as $svalue) {
			if(!Yii::app()->user->checkAccess($svalue['access']))
				continue;

			if($id) {
				if($svalue['parrent'] == $id) {
					$this->active[$_k][] = substr($svalue['_key'], 1);
					$svalue['smenu']     = $this->_setMenu($menu, $svalue['id'], $_k);
					$list[]              = $svalue;
				}
			}
			else {

				if(!$svalue['parrent']) {
					$this->mainMenu[] = $svalue['nactive'];
					$svalue['smenu']  = $this->_setMenu($menu, $svalue['id'], $svalue['nactive']);
					$list[]           = $svalue;
				}
			}
		}

		return $list;
	}

	protected function getMenuData()
	{
		$this->menu = Yii::app()->cache->get(md5("menu_getMenuData" . Yii::app()->user->_get('role')));

		if($this->menu === false) {
			$_list = Yii::app()->db->createCommand()->select('*')->from('{{menu}}')->order('nsort ASC')->queryAll();
			$data  = $this->_setMenu($_list);

			$klist = array();

			foreach($_list as $k => $v) {
				$klist[$v['nactive']][] = $v;
			}

			$this->menu = array('_klist' => $klist, '_list' => $_list, 'list' => $data, 'active' => $this->active,
				'main' => $this->mainMenu);

			Yii::app()->cache->set(md5("menu_getMenuData" . Yii::app()->user->_get('role')), $this->menu, 24 * 60 * 60);

			unset($data);
			unset($klist);
			unset($_list);
			$this->_clear();
		}
	}

	protected function _clear()
	{
		$this->active   = array();
		$this->mainMenu = array();
	}

}
