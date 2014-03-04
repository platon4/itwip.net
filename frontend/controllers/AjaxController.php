<?php

class ajaxController extends Controller
{
	public function filters()
	{
		return array(
			'accessControl',
			'ajaxOnly'
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('favmenu', '_messages', 'bell'),
				'roles' => array('user'),
			),
			array('deny',
				'users' => array('*'),
			),
		);
	}

	public function action_messages($act = '')
	{
		$db     = Yii::app()->db;
		$dcount = 0;

		if($act == 'remove') {
			$rmessages = (isset($_POST['_message']) AND is_array($_POST['_message'])) ? $_POST['_message'] : array();

			if((isset($rmessages['message']) && count($rmessages['message'])) || (isset($rmessages['support']) && count($rmessages['support']))) {
				$mids = array();

				if(isset($rmessages['message'])) {
					foreach($rmessages['message'] as $md) {
						if(intval($md))
							$mids[] = intval($md);
					}
				}
				if(count($mids)) {
					$sth       = $db->createCommand("DELETE FROM {{messages}} WHERE id IN('" . implode("', '", $mids) . "') AND owner_id='" . Yii::app()->user->id . "'");
					$ncountRow = $sth->execute();
					$dcount    = $ncountRow;
				}

				if(isset($rmessages['support'])) {
					foreach($rmessages['support'] as $sd) {
						if(intval($sd)) {
							$id = intval($sd);

							$sth     = $db->createCommand("DELETE FROM {{tickets}} WHERE id='" . $id . "' AND owner_id='" . Yii::app()->user->id . "'");
							$coutRow = $sth->execute();

							if($coutRow) {
								$sth     = $db->createCommand("DELETE FROM {{tickets_messages}} WHERE ticket_id='" . $id . "'");
								$coutRow = $sth->execute();

								$dcount++;
							}
						}
					}
				}

				$users = User::model()->findByPk(Yii::app()->user->id);
				$users->saveCounters(array('mail_all' => -$dcount));
			}
			else {
				echo json_encode(array('code' => 201, 'html' => Yii::t('main', '_confirm_remove_message_no_messages')));
				Yii::app()->end();
			}
		}

		$_s      = $db->createCommand("SELECT * FROM {{tickets}} WHERE owner_id='" . Yii::app()->user->id . "' AND  _status!=3 ORDER BY user_read ASC, _date DESC LIMIT 10")->queryAll();
		$_scount = count($_s);
		$_limit  = ($_scount >= 5) ? 5 : 10;

		$_m      = $db->createCommand("SELECT * FROM {{messages}} WHERE owner_id='" . Yii::app()->user->id . "' ORDER BY  _date DESC LIMIT " . $_limit)->queryAll();
		$_mcount = count($_m);

		$messages = array();

		$_c = 0;
		foreach($_s as $support) {
			$_c++;

			$support['type']   = 'support';
			$support['_title'] = $support['_subject'];
			unset($support['_subject']);
			$messages[] = $support;

			if(($_mcount >= 5) OR (($_c + $_mcount) >= 10))
				break;
		}

		foreach($_m as $message) {
			$_c++;

			$message['type'] = 'message';
			$messages[]      = $message;

			if($_c >= 10)
				break;
		}

		ArrayHelper::orderBy($messages, '_date', SORT_DESC);

		switch($act) {
		case "remove":
			$template = '_messages_list';
			break;

		case "messages":
			$template = '_messages_list';
			break;

		default:
			$template = 'messages';
		}

		$count = count($messages);

		echo json_encode(array('code' => 200, 'count' => $count, 'dcount' => $dcount, 'html' => $this->renderPartial($template, array(
				'count' => $count, 'messages' => $messages), true)));
		Yii::app()->end();
	}

	public function actionBell()
	{
		$this->widget('application.widgets.Bell');
	}

	public function actionFavMenu()
	{
		$fID = (isset($_REQUEST['_fID']) AND intval($_REQUEST['_fID'])) ? intval($_REQUEST['_fID']) : 0;

		$favList  = explode(",", Yii::app()->user->_get('favMenu'));
		$favSave  = array();
		$is_found = false;
		$status   = 0;

		foreach($favList as $k => $id) {
			$id = intval($id);

			if($id) {
				if($id == $fID) {
					$is_found = true;
				}
				else
					$favSave[] = $id;
			}
		}

		if(!$is_found) {
			$favSave[] = $fID;
			$status    = 1;
		}

		$flist = (count($favSave)) ? implode(",", $favSave) : '';

		Yii::app()->db->createCommand("UPDATE {{accounts}} SET favMenu=:fav WHERE id=:id")->execute(array(
			':id' => Yii::app()->user->id, ':fav' => $flist));
		JSON::encode(array('fid' => $fID, 'status' => $status, 'favMenu' => $this->renderPartial('favMenu', array(
				'flist' => $flist), true)));
	}
}
