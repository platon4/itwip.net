<?php

class WebUser extends CWebUser {

    private $_model;
    private $_settings;
    private $_loyalt;
    public $identityCookie=array(
        'path'=>'/',
        'domain'=>'.itwip.net',
    );

    public function init()
    {
        $conf                =Yii::app()->session->cookieParams;
        $this->identityCookie=array(
            'path'=>$conf['path'],
            'domain'=>$conf['domain'],
        );
        parent::init();
    }

    public function getName()
    {
        $user=$this->loadUser();

        return $user->name;
    }

    public function _get($key)
    {
        $user=$this->loadUser();

        if($user)
        {
            if(isset($user->$key))
            {
                return $user->{$key};
            } else
            {
                throw new CHttpException(500,Yii::t('main','_missing_user_data',array(
                    '{key}'=>$key)));
            }
        } else
        {
            Yii::app()->user->logout();

            if(Yii::app()->request->isAjaxRequest)
            {
                echo "Access diented.";
                Yii::app()->end();
            } else
            {
                Yii::app()->request->redirect(Yii::app()->homeUrl);
            }
        }
    }

    public function _getLoyalt($key,$id=0)
    {
        if(!$id)
            $id=Yii::app()->user->id;

        if($this->_loyalt === null)
        {
            $this->_loyalt=Yii::app()->db->createCommand("SELECT * FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true,array(
                ':id'=>$id));
        }

        return isset($this->_loyalt[$key])?$this->_loyalt[$key]:'';
    }

    public function getSettings($id=0)
    {
        if($this->_settings === null)
        {
            $this->_settings=@unserialize($this->loadUser($id)->_settings);
        }

        return $this->_settings;
    }

    public function _setting($key,$id=0)
    {
        $settings=$this->getSettings($id);

        return isset($settings[$key])?$settings[$key]:false;
    }

    public function _getBalance()
    {
        $user=$this->loadUser();

        return round($user->money_amount + $user->bonus_money,2);
    }

    function getRole()
    {
        if($user=$this->loadUser())
        {
            // в таблице User есть поле role
            return $user->role;
        }
    }

    // Load user model.
    private function loadUser($id=0)
    {
        if($this->_model === null)
        {
            if(!Yii::app()->user->isGuest)
                $this->_model=User::model()->findByPk(Yii::app()->user->id);
            elseif($id)
                $this->_model=User::model()->findByPk($id);
        }

        return $this->_model;
    }

    public function getReturnUrl($defaultUrl=null)
    {
        
        if($defaultUrl === null)
        {
            $defaultReturnUrl=Yii::app()->getUrlManager()->showScriptName?Yii::app()->getRequest()->getScriptUrl():Yii::app()->getRequest()->getBaseUrl().'/';
        } else
        {
            $defaultReturnUrl=CHtml::normalizeUrl($defaultUrl);
        }
        return $this->getState('__returnUrl',$defaultReturnUrl);
    }

}
