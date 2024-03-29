<?php

class ShopModule extends CWebModule {

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application
        // import the module-level models and components
        $this->setImport(array(
            'shop.models.*',
            'shop.components.*',
        ));
    }

    public function beforeControllerAction($controller,$action)
    {
        if(parent::beforeControllerAction($controller,$action))
        { 
            Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/m-coreZ3w.js');
            return true;
        } else
            return false;
    }

}
