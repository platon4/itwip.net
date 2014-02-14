<?php

class lastAdminTopics extends CWidget {

    public $limit       =5;
    public $title_length=60;

    public function run()
    {
        $topics=Yii::app()->fdb->createCommand('SELECT title,pubdate,seolink FROM {{content}} WHERE category_id=16 AND is_arhive=0 ORDER BY pubdate DESC LIMIT '.$this->limit)->queryAll();
        $this->render('lastAdminTopics',array('topics'=>$topics,'length'=>$this->title_length));
    }

}
