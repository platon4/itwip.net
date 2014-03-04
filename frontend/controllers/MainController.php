<?php

class mainController extends Controller {

    public function actions()
    {
        return array(
            'captcha'=>array(
                'class'=>'CCaptchaAction',
                'backColor'=>0xC7C4C5,
                'transparent'=>true,
                'minLength'=>'6',
                'maxLength'=>'8',
                'testLimit'=>3,
                'height'=>'45',
            ),
        );
    }

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('help','regulations','index','support','jslang',
                    'error','captcha','incompatibility'),
                'users'=>array('*'),
            ),
            array('deny',
                'users'=>array('*'),
            ),
        );
    }

    public function actionHelp()
    {
        $this->render('application.views.main.info',array('title'=>'Sorry','message'=>'this page is under construction.'));
    }

    public function actionIncompatibility()
    {
        $this->renderPartial('_incompatibility');
    }

    public function actionRegulations()
    {
        if(Yii::app()->user->isGuest)
        {
            $this->render('regulations');
        } else
        {
            $this->render('_regulationsText');
        }
    }

    public function actionIndex()
    {
        if(!Yii::app()->user->isGuest)
        {
            $this->render('_authorized');
        } else
        {
            $this->render('_unauthorized');
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->user->checkAccess('admin'))
            {
                if(Yii::app()->request->isAjaxRequest)
                {
                    echo $error['message'];
                    Yii::app()->end();
                } else
                    $this->render('error',array('code'=>$error['code'],'message'=>$error['message']));
            } else
            {
                if(Yii::app()->request->isAjaxRequest)
                {
                    echo Yii::t('main','page_not_available');
                    Yii::app()->end();
                } else
                    $this->render('error',array('code'=>$error['code'],'message'=>Yii::t('main','page_not_available')));
            }
        }
    }

    /**
     * JS lang.
     */
    public function actionJsLang()
    {
        $this->renderPartial('jsLang');
    }

}
