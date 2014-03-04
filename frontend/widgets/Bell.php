<?php

class Bell extends CWidget {

    public function run()
    {
        if(!Yii::app()->request->isAjaxRequest)
        {
            $bell=Yii::app()->cache->get(md5("_hBell".Yii::app()->user->id));

            if($bell===false)
            {
                $bell=array('count'=>Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts}} a INNER JOIN {{tw_accounts_settings}} s ON a.id=s.tid WHERE a.owner_id=:id AND s.working_in=0")->queryScalar(array(':id'=>Yii::app()->user->id)));
                Yii::app()->cache->set(md5("_hBell".Yii::app()->user->id),$bell,15 * 60);
            }

            if($bell['count']==0) return false;
                
            $this->render('bell',array('requests_count'=>$this->getCount()));
        }
        else {
                echo json_encode(array('code'=>200,'count'=>$this->getCount()));
                Yii::app()->end();           
        }
    }
    
    public function getCount()
    {
       return Yii::app()->db->createCommand("SELECT COUNT(*) as count FROM {{tweets_to_twitter}} tt INNER JOIN {{tw_accounts}} a ON tt._tw_account=a.id WHERE tt.approved=0 AND tt.status=0 AND a.owner_id=:id")->queryScalar(array(
                ':id'=>Yii::app()->user->id)); 
    }
}
