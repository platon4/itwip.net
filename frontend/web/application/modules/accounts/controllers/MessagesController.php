<?php

class MessagesController extends Controller
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index','getMessage','_remove','_masssystemaction','_close','_new'),
                'roles'=>array('user'),
            ),
			array('deny',
				'users'=>array('*'),
			),
        );
    }
	public function actionIndex($_action='',$_o=false,$_d=false)
	{
		 if(!Yii::app()->request->isAjaxRequest)
			Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/www-messages.js');

        $criteria = new CDbCriteria;
        $criteria->condition='owner_id='.Yii::app()->user->id;

        $order=array('status'=>'_is_read','date'=>'_date');
        $oMethod=($_d=='ASC')?'ASC':'DESC';

        if($_o AND array_key_exists($_o,$order))
            $criteria->order=(string)$order[$_o].' '.$oMethod;
        else
            $criteria->order='_is_read ASC, _date DESC';

        $all_system_messages=Messages::model()->count('owner_id='.Yii::app()->user->id);
        $new_system_message=Messages::model()->count('owner_id='.Yii::app()->user->id.' AND _is_read=0');        
		
		$all_support_messages=Support::model()->count('owner_id='.Yii::app()->user->id);
        $new_support_message=Support::model()->count('owner_id='.Yii::app()->user->id.' AND user_read=0');

        $pages=new CPagination($all_system_messages);
        $pages->pageSize=10;
		$pages->validateCurrentPage=false;
        $pages->applyLimit($criteria);

      	$messages=Messages::model()->findAll($criteria);
		
        if(Yii::app()->request->isAjaxRequest)
        {
            if(count($messages))
            {
                echo json_encode(array('code'=>200,'messages'=>$this->renderPartial('_messages_system',array('messages'=>$messages),true)));
            }
            else
               echo json_encode(array('code'=>404));

            Yii::app()->end();
        }
        else {
			$criteria = new CDbCriteria;
			$criteria->condition='owner_id='.Yii::app()->user->id;		
			$criteria->order='_status ASC, _date DESC';		

			$supports=Support::model()->findAll($criteria);
		
      	    $this->render('index', array(
												'new_system_message'=>$new_system_message,
												'all_system_messages'=>$all_system_messages,												
												'new_support_message'=>$new_support_message,
												'all_support_messages'=>$all_support_messages,
												'messages'=>$messages,
												'supports'=>$supports,
											));
		}
	}

    public function actiongetMessage($id,$act='')
    {
       if(intval($id))
       {
			switch($act)
			{
				case "support":
						$response=$this->getSupportMessage($id);
					break;
					
				default:
					 $response=$this->getSystemMessage($id);
			}
       }
       else
         $response=array('code'=>'403','message'=>Yii::t('yii', 'Your request is invalid.'));

        echo json_encode($response);
        Yii::app()->end();
    }
	
	public function action_new($id,$act='')
	{
		$text=(isset($_POST['text']) AND trim($_POST['text']) !='')?$_POST['text']:false;
		
		if(intval($id) AND $text)
		{
			switch($act)
			{
				case "support":
						$response=$this->_newSupportMessage($id,$text);
					break;
				
				default:
					$response=array('code'=>'403','message'=>Yii::t('yii', 'Your request is invalid.'));
			}
		}
		else
			$response=array('code'=>'403','message'=>Yii::t('yii', 'Your request is invalid.'));
			
        echo json_encode($response);
        Yii::app()->end();			
	}
	
    public function action_remove($id,$act='')
    {
       if(intval($id))
       {
			
			if($act=='support')
				$message=Support::model()->findByPk($id,'owner_id='.Yii::app()->user->id);
			else
				$message=Messages::model()->findByPk($id,'owner_id='.Yii::app()->user->id);

        if($message!==null)
        {
             $is_read=1;
			 
			 if($act=='support')
			 
				$is_read=($message->user_read==0)?0:1;
			 else
				$is_read=($message->_is_read==0)?0:1;
				
             if($is_read==0)
             {
                 $sql='mail_all=mail_all-1, mail_unread=mail_unread-1';
             }
             else
               $sql='mail_all=mail_all-1';

            $command = Yii::app()->db->createCommand('UPDATE {{accounts}} SET '.$sql.' WHERE id = :id');
            $command -> execute(array(':id' => Yii::app()->user->id));

			if($act=='support')
			{
				$command = Yii::app()->db->createCommand('DELETE FROM {{tickets_messages}} WHERE ticket_id= :id');
				$command -> execute(array(':id' => $message->id));				
			}		 
			  
            $message->delete();			
            $response=array('code'=>'200','message'=>Yii::t('accountsModule.message','message_successfully_removed'));
        }
          else
            $response=array('code'=>'404','message'=>Yii::t('yii', 'The requested page does not exist.'));
       }
       else
         $response=array('code'=>'403','message'=>Yii::t('yii', 'Your request is invalid.'));

        echo json_encode($response);
        Yii::app()->end();
    }
	
	public function action_close($id)
	{
		$command = Yii::app()->db->createCommand('UPDATE {{tickets}} SET _status=3 WHERE id = :id AND owner_id=:owner_id');
		$rowCount=$command -> execute(array(':id'=>$id, ':owner_id' => Yii::app()->user->id));
		
		if($rowCount)
			echo json_encode(array('code'=>200));
		else
			echo json_encode(array('code'=>0));
		
		Yii::app()->end();
	}
	
    public function action_massSystemAction()
    {
        $action=(isset($_POST['messageAction']))?$_POST['messageAction']:false;
        $messages=(isset($_POST['message']))?$_POST['message']:array();
        $response=array('code'=>200);

        if(is_array($messages) AND count($messages))
        {
            if($action=='remove')
            {
               $del=array();
               foreach($messages as $message)
               {
                   $mID=intval($message);
                   $del[]=$mID;
               }

               if(count($del))
               {
                    $command=Yii::app()->db->createCommand("DELETE FROM {{messages}} WHERE id IN('".implode("','",$del)."') AND owner_id=:owner_id");
                    $rowCount=$command->execute(array(':owner_id'=>Yii::app()->user->id));
               }

               if($rowCount)
               {
                    $new_system_message=$rowCount;

                    $command = Yii::app()->db->createCommand('UPDATE {{accounts}} SET mail_all=mail_all-:mail_all, mail_unread=mail_unread-:mail_unread WHERE id = :id');
                    $command -> execute(array(':mail_unread'=>$new_system_message,':mail_all'=>$rowCount,':id' => Yii::app()->user->id));

                    $response=array('code'=>200,'_all_unread'=>$new_system_message,'_all'=>$rowCount,'action'=>'remove','ids'=>$del);
               }
               else
                  $response=array('code'=>502,'message'=>Yii::t('accountsModule.message', '_mass_action_no_remove_messages'));
            }
            else if($action=='all_unread')
            {

            }
            else if($action=='all_read')
            {

            }
            else
                $response=array('code'=>'403','message'=>Yii::t('accountsModule.message', '_mass_action_no_selected'));
        }
        else
             $response=array('code'=>'403','message'=>Yii::t('accountsModule.message', '_mass_action_no_message_select'));

        echo json_encode($response);
        Yii::app()->end();
    }
	
	/**
	 * Private functions
	 */
	private function _newSupportMessage($id,$text)
	{
		$_m=Support::model()->findByPk($id,'owner_id='.Yii::app()->user->id);

		 if($_m!==null)
		 {
			if($_m->_status==3)
			{
				$response=array('code'=>'404','message'=>Yii::t('accountsModule.messages', 'This ticket is closed.'));
			}
			else {
			
				$command = Yii::app()->db->createCommand('INSERT INTO {{tickets_messages}} (ticket_id,_text,_date) VALUES (:ticket_id,:_text,:_date)');
				$rowCount=$command -> execute(array(':ticket_id' => $id,':_text'=>$text,':_date'=>date("Y-m-d H:i:s")));		
				
				if($rowCount)
				{
					$command = Yii::app()->db->createCommand('UPDATE {{tickets}} SET _status=0,_is_remove=0 WHERE id = :id');
					$command -> execute(array(':id' => $_m->id));
					
					Notifications::admins(false,Yii::t('accountsModule.message','_new_request_reply_support')."\n----------------------------------\nТема:\n".$_m->_subject."\n----------\nТекст:\n".$text);
					
					$response=array('code'=>'200','html'=>$this->renderPartial('_support_new_result',array('text'=>$text,'_date'=>time()),true));
				}	
				else
					$response=array('code'=>'201','message'=>Yii::t('accountsModule.messages', '_support_error_new_message.'));
			}
		}
		  else
			$response=array('code'=>'404','message'=>Yii::t('yii', 'The requested page does not exist.'));
			
		return $response;		
	}
	
	 private function getSystemMessage($id)
	 {
          $message=Messages::model()->findByPk($id,'owner_id='.Yii::app()->user->id);

          if($message!==null)
          {
             if($message->_is_read==0)
             {
                Messages::model()->updateByPk($id,array('_is_read'=>1));
                $command = Yii::app()->db->createCommand('UPDATE {{accounts}} SET mail_unread = mail_unread-1 WHERE id = :id');
                $command -> execute(array(':id' => Yii::app()->user->id));
                $is_read=0;
             }
             else
                $is_read=1;

             $response=array('code'=>'200','message'=>$this->renderPartial('_read_system',array('message'=>$message),true),'is_read'=>$is_read);
          }
           else
             $response=array('code'=>'404','message'=>Yii::t('yii', 'The requested page does not exist.'));	
			
			return $response;
	 }
	 
	 private function getSupportMessage($id)
	 {
         $_m=Support::model()->findByPk($id);

         if($_m!==null)
         {
            if($_m->user_read==0)
            {
                Support::model()->updateByPk($id,array('user_read'=>1));
                $command = Yii::app()->db->createCommand('UPDATE {{accounts}} SET mail_unread = mail_unread-1 WHERE id = :id');
                $command -> execute(array(':id' => Yii::app()->user->id));
                $is_read=0;
            }
             else
                $is_read=1;

                $command = Yii::app()->db->createCommand('SELECT * FROM {{tickets_messages}} WHERE ticket_id=:t_id ORDER BY id ASC');
                $_ms=$command->queryAll('fetchAll',array(':t_id' => $_m->id));		
				
            $response=array('code'=>'200','message'=>$this->renderPartial('_read_support',array('_ms'=>$_ms,'_m'=>$_m),true),'is_read'=>$is_read);
        }
          else
            $response=array('code'=>'404','message'=>Yii::t('yii', 'The requested page does not exist.'));	
			
			return $response;
	 }
}