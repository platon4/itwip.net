<?php

class PTweets extends CFormModel
{	
    public $_text;
    public $_status;
    
	public function rules()
	{
		return array(
			 array('_text', 'length', 'max'=>1000),
			 array('_status', 'numerical','integerOnly'=>true,'min'=>1,'max'=>10, 'on'=>'process'),
		);
	}	
    
    public function _hash($text)
    {        
        return md5(trim($text));
    }
}
