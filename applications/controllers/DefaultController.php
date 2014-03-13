<?php

namespace app\controllers;

class DefaultController extends \app\components\Controller
{
    public function actionIndex()
    {
        echo 'Error';
        die();
        $this->redirect('https://www.itwip.net/');
    }
}