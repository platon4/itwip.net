<?php

class _language extends CWidget
{
	private $_currentUrl = '';
	
	public function init($request)
	{
		$_lang = null;
		$_langName = array('ru' => 'Русский', 'en' => 'English');

		foreach(Yii::app()->params->languages as $key)
		{	
			$_SERVER['REQUEST_URI'] = str_replace("&_l=" . $key, "", $_SERVER['REQUEST_URI']);
			$_SERVER['REQUEST_URI'] = str_replace("?_l=" . $key, "", $_SERVER['REQUEST_URI']);
		}
		
		foreach(Yii::app()->params->languages as $key)
		{		
			if(count($rt3) == 2)
			{
				$link = htmlspecialchars($_SERVER['REQUEST_URI']) . '&_l=' . $key;
			}
			else {
				$link = htmlspecialchars($_SERVER['REQUEST_URI']) . '?_l=' . $key;
			}
			
			$_lang .= Html::link('', $link, array('class' => $key));
		}
		
		echo $_lang;
	}
}