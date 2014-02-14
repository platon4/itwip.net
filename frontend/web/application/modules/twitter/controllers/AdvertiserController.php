<?php

class AdvertiserController extends Controller
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
                'actions'=>array('projects'),
                'users'=>array('@'),
            ),
            array('deny',
                'actions'=>array('projects'),
                'users'=>array('*'),
            ),
        );
    }

	public function actionProjects()
	{
		$this->render("projects");
	}
}