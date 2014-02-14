<?php

class AccountsModule extends CWebModule
{
    public function init()
    {
        $this->setImport(array(
            'accounts.models.*',
            'accounts.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
            return true;
        else
            return false;
    }

}
