<?php

class Sitemap extends CActiveRecord
{	
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return '{{tw_tweets_sitemap}}';
    }
	public function rules()
	{
		return array(
			 array('_text', 'safe'),
		);
	}
}
