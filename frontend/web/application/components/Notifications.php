<?php

class Notifications 
{
	
	public static function admins($ids=0,$messages)
	{
		if(intval($ids) OR (is_array($ids) AND count($ids)))
		{
			if(is_array($ids))
				$w="id IN('".implode("', '",$ids)."')";
			else
				$w="id=".intval($ids);
				
			$admins=Yii::app()->db->createCommand("SELECT email,_settings FROM {{accounts}} WHERE {$w} AND (role='moderator' OR role='admin')")->queryAll();
		}
		else
			$admins=Yii::app()->db->createCommand("SELECT email,_settings FROM {{accounts}} WHERE role='moderator' OR role='admin'")->queryAll();
		
		if(count($admins))
		{
			foreach($admins as $admin)
			{
				$settings=unserialize($admin['_settings']);
				
				if($settings['icq_new_snotification'] AND trim($settings['_icq']) !='')
				{
					$message=trim($settings['_icq'])."||".$messages;
					$fp=fopen(APP_DIR.'/cron/icq/messages/'.md5(time().$admin['email'].rand(0,50)).'.txt','wb+');
					fwrite($fp,$message);
					fclose($fp);
				}
			}
		}
	}
}