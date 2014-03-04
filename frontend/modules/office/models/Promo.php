<?php

class Promo extends CFormModel
{
	public $tie;
	public $amount;
	public $limit;
	public $_count;
	public $mark;
	
	public function rules(){
		return array(
			array('amount', 'required'),
			array('amount', 'numerical','integerOnly'=>true,'min'=>1),
			
			array('_count', 'numerical','integerOnly'=>true,'min'=>1,'on'=>'simple'),
			
			array('tie,mark', 'required','on'=>'adavance'),
			
			array('limit', 'numerical','integerOnly'=>true,'min'=>0,'on'=>'adavance'),
			array('tie', 'numerical','integerOnly'=>true,'min'=>1,'on'=>'adavance','tooSmall'=>'Пользователя с таким ID не может быть'),
		);
	}
}