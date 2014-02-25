<?php

class Underconstruct
{
    public function init()
    {
        $underconstruct = TRUE;

        if(Yii::app()->user->checkAccess('tester') && !Yii::app()->user->isGuest)
            $underconstruct = FALSE;
        elseif(strpos(Yii::app()->getRequest()->getUrl(), '/accounts/auth') === 0)
            $underconstruct = FALSE;

        if($underconstruct === TRUE) {
            include Yii::app()->getViewPath() . '/underconstruct.php';
            Yii::app()->end();
        }
    }
}