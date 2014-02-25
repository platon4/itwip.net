<?php

class Underconstruct
{
    public function init()
    {
        $underconstruct = TRUE;

        if(Yii::app()->user->checkAccess('tester') && !Yii::app()->user->isGuest)
            $underconstruct = FALSE;
        elseif(Yii::app()->getRequest()->getUrl() == '/accounts/auth')
            $underconstruct = FALSE;

        if($underconstruct === TRUE) {
            include Yii::app()->getViewPath() . '/underconstruct.php';
            Yii::app()->end();
        }
    }
}