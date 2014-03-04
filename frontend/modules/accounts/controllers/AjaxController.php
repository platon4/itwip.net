<?php

class ajaxController extends Controller
{
	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor' => 0xC7C4C5,
				'transparent'=>true,
				'minLength' => '6',
				'maxLength' => '8',
				'testLimit'=> 1,
				'width' => '167',
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
            array('deny',
                'actions'=>array('*'),
            ),            
        );
    }
}