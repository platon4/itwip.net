<?php

class DateValidator extends CFormModel
{
	public $_from;
	public $_to;
	
	public function rules()
	{
		return array(
			 array('_from', 'date', 'format'=>'yyyy-MM-dd'),
			 array('_to', 'date', 'format'=>'yyyy-MM-dd'),
		);
	}	
}
