<?php

class addAccounts extends CFormModel
{
	public $agreed;
	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }	
	
	public function rules()
	{
		return array(
			array('agreed', 'compare', 'compareValue' => true, 'message' => Yii::t('twitterModule.accounts', '_twitterAccountAdd_agreed')),
		);
	}
}