<?php

class UsersController extends Controller
{
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
                'actions'=>array('index'),
                'roles'=>array('admin'),
            ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),          
        );
    }
	
	public function actionIndex()
	{	
		$this->render('index');
	}		
}