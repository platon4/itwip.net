<?php

class Parser
{
	protected $html;
	protected $errors;
	
	public function __construct()
	{
		Yii::import('application.components.SimpleHtmlDom', true);
	}
	
	public function init($data, $from_url = false)
	{		
		if($from_url AND preg_match('/^http|https:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i',$data))
		{	
			$curl = CHelper::_getURL($data);
			
			if($curl['code'] == 200)
			{
				$html = $curl['response'];
			}
			else 
			{
				if($curl['error_code'] == 28)
				{
					$this->setError(Yii::t('internal', '_conection_timedout'), 28);
				} else
					$this->setError($data . ' ' . $curl['error'], $curl['code']);
			}
				
			$error_html=Yii::t('internal', 'Error parse this link {link}', array('{link}' => $data));
			unset($curl);
		}
		else {
			$html = $data;
			$error_html=Yii::t('internal', 'Error parse the html.');
		}

		if(!count($this->errors))
		{			
			$this->html = str_get_html($html);
			
			if(!is_object($this->html))
				$this->setError($error_html, 6);
		}		
	}
	public function get($find, $get = '')
	{
		switch($get)
		{
			case "plain":
					$data = $this->html->find($find, 0)->plaintext;
				break;			
			case "inner":
					$data = $this->html->find($find, 0)->innertext;
				break;
			case "out":
					$data = $this->html->find($find, 0)->outertext;
				break;
				
			default:
				$data = $this->html->find($find);
		}
		
		return $data;
	}
	public function clear()
	{
		if(is_object($this->html))
			$this->html->clear();
		
		$this->errors = array();
		$this->html = null;
	}
	public function validate()
	{
		return (count($this->errors)) ? false : true;
	}
	public function getError($key)
	{	
		return (isset($this->errors[0][$key])) ? $this->errors[0][$key] : null;
	}
	public function getErrors()
	{
		return $this->errors;
	}
	public function setError($text, $code = 0)
	{
		$this->errors[] = array('error' => $text, 'code' => $code);
	}
}