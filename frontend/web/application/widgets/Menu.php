<?php

class Menu extends CWidget
{
    public $breadcrumbs = [];
    public $favMenu = [];
    public $menu = [];
    public $ajax = FALSE;
    public $active = [];
    public $mainMenu = [];
    public $addonsList;
    public $activeBlock;

    public function init()
    {
        $this->getMenuData();
        $this->getFavoritsList();

        if(!$this->ajax)
            $this->render('userMenu', array('userMenu' => $this->menu['list']));
        else
            $this->render('favMenu', array('userMenu' => $this->menu['list']));
    }

    public function getMenu()
    {
        $i = 0;

        foreach($this->menu['list'] as $element) {

            $i++;

            $params = ["data-active=\"con_m_" . $i . "\""];

            if($i === 1)
                $params[] = "style=\"border-top: none;\"";

            if($this->activeBlock == $element['_active'])
                $params[] = "class=\"active no_remove\"";

            if($element['url'] == '/')
                $link = '/';
            else
                $link = !empty($element['url']) ? $element['url'] : 'javascript:;';

            echo '<li ' . implode(" ", $params) . '><a href="' . $link . '" id="m_' . $i . '"><span class="menu_icon"><i class="' . $element['icon'] . '"></i></span><span class="menu_alt"> ' . Yii::t('menu', $element['_key']) . '</span></a>' . Html::openTag('div', array('class' => 'gradient')) . Html::closeTag('div') . '</li>';
        }
    }

    public function getParentMenu($data, $parent = FALSE)
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
                        'id'      => 'fav_' . $element['id'], 'class' => 'like_menu fav-icon' . $favActive,
                        'onclick' => '_addFav(\'' . $element['id'] . '\', this); return false;')) . Html::closeTag('span'), $element['url']);
            } else {
                echo Yii::t('menu', $element['_key']);
            }

            echo Html::closeTag('li');

            if(isset($element['smenu']) && count($element['smenu'])) {
                $this->getParentMenu($element['smenu'], TRUE);
            }
        }

        echo CHtml::closeTag('ul');
    }

    public function getParentMenuSelect($data, $level = 0)
    {
        $level = $level + 1;

        $separator = NULL;

        for($e = 0 ; $e < $level ; $e++) {
            $separator .= "-";
        }

        foreach($data as $element) {
            if(!in_array($element['id'], $this->favMenu)) {
                if(trim($element['url']) == "" OR !$element['parrent']) {
                    $disabled = "disabled";
                } else {
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
                    } else {
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

    protected function _setMenu($menu, $id = 0, $_k = FALSE)
    {
        $list = [];

        foreach($menu as $svalue) {
            if(!Yii::app()->user->checkAccess($svalue['access']))
                continue;

            if($id) {
                if($svalue['parrent'] == $id) {
                    $this->active[$_k][] = substr($svalue['_key'], 1);
                    $svalue['smenu'] = $this->_setMenu($menu, $svalue['id'], $_k);
                    $list[] = $svalue;
                }
            } else {
                if(!$svalue['parrent']) {
                    $this->mainMenu[] = $svalue['_active'];
                    $svalue['smenu'] = $this->_setMenu($menu, $svalue['id'], $svalue['_active']);
                    $list[] = $svalue;
                }
            }
        }

        return $list;
    }

    protected function getMenuData()
    {
        $this->menu = Yii::app()->cache->get(md5("menu_getMenuData" . Yii::app()->user->_get('role')));

        if($this->menu === FALSE) {
            $_list = Yii::app()->db->createCommand()->select('*')->from('{{menu}}')->order('nsort ASC')->queryAll();
            $data = $this->_setMenu($_list);

            $klist = array();

            foreach($_list as $k => $v) {
                $klist[$v['_active']][] = $v;
            }

            $this->menu = array('_klist' => $klist, '_list' => $_list, 'list' => $data, 'active' => $this->active,
                                'main'   => $this->mainMenu);

            Yii::app()->cache->set(md5("menu_getMenuData" . Yii::app()->user->_get('role')), $this->menu, 24 * 60 * 60);

            unset($data);
            unset($klist);
            unset($_list);
            $this->_clear();
        }
    }

    protected function _clear()
    {
        $this->active = [];
        $this->mainMenu = [];
    }
}
