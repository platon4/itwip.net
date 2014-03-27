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
                'actions' => array('delivery'),
                'roles'   => array('admin'),
            ),
            array('allow',
                'actions' => array('support', '_get', '_getMessage', '_setimportance',
                    'remove', '_process', '_new'),
                'roles'   => array('moderator'),
            ),
            array('allow', // deny all users
                'actions' => array('rassilka'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionRassilka()
    {
        ini_set('max_execution_time', 0);
        $id = Yii::app()->redis->get('rassid');

        $accounts = Yii::app()->db->createCommand("SELECT id,email FROM {{accounts}} WHERE status=1 AND id>:id LIMIT 100")->queryAll(true, [':id' => $id]);

        $email = Yii::app()->email;

        foreach($accounts as $row) {
            Yii::app()->redis->set('rassid', $row['id']);

            $email->to = $row['email'];
            $email->view = "_";
            $email->viewVars = array();
            $email->from = "info@itwip.ru";

            $email->subject = "Обновление сервиса";

            $email->send();
            echo $row['id'] . "  -  " . $row['email'] . PHP_EOL;
        }
    }

    public function actionDelivery()
    {
        $this->render('delivery');
    }

    public function action_new($id)
    {
        $ticket = Tickets::model()->findByPk($id);
        $ticket->scenario = 'new';

        $ticket->attributes = array(
            'text' => $_POST['_text'],
        );

        if($ticket->validate()) {
            Yii::app()->db->createCommand("INSERT INTO {{tickets_messages}} (ticket_id,ot_id,_text,_date) VALUES (:ticket_id,:ot_id,:_text,:_date)")
                ->execute(array(
                    ':ticket_id' => $ticket->id,
                    ':ot_id'     => Yii::app()->user->id,
                    ':_text'     => $ticket->text,
                    ':_date'     => date('Y-m-d H:i:s'),
                ));

            $sth = Yii::app()->db->createCommand("UPDATE {{tickets}} SET _status=2 WHERE id=:id");
            $rowCount = $sth->execute(array(':id' => $id));

            if($ticket->user_read == 1) {
                $user = Users::model()->findByPk($ticket->owner_id);
                $user->saveCounters(array('mail_unread' => 1));
            }

            $ticket->_status = 2;
            $ticket->user_read = 0;
            $ticket->_reply = serialize(array('id'   => Yii::app()->user->id,
                                              'name' => Yii::app()->user->_get('name')));
            $ticket->_date_last_answer = date('Y-m-d H:i:s');

            $ticket->save();

            $html = $this->renderPartial('_reply_fast', array('name' => Yii::app()->user->_get('name'),
                                                              'text' => $ticket->text), true);
            $code = 200;
        } else {
            $code = 201;
            $html = CHtml::error($ticket, 'text');
        }

        echo json_encode(array('html' => $html, 'code' => $code));
        Yii::app()->end();
    }

    public function actionSupport($act = '')
    {
        if(Yii::app()->request->isAjaxRequest AND $act) {
            switch($act) {
                default:
                    $messages = $this->_get($act);
            }

            echo json_encode(array('html' => $this->renderPartial('_messages', array(
                    'messages' => $messages), true)));
            Yii::app()->end();
        } else {
            Yii::app()->clientScript->registerScriptFile('/js/_a/www-o3e-support.js');

            $countAll = Yii::app()->db->createCommand("SELECT _to, COUNT(*) as count FROM {{tickets}} GROUP BY _to")->queryAll();
            $countUnread = Yii::app()->db->createCommand("SELECT _to, COUNT(*) as count FROM {{tickets}} WHERE _status=0 AND _is_remove=0 GROUP BY _to")->queryAll();

            $countArr = array(
                0 => array('all' => 0, 'unread' => 0),
                1 => array('all' => 0, 'unread' => 0),
                2 => array('all' => 0, 'unread' => 0),
                3 => array('all' => 0, 'unread' => 0),
            );

            foreach($countAll as $_c) {
                if(isset($countArr[$_c['_to']]))
                    $countArr[$_c['_to']]['all'] = $_c['count'];
            }

            foreach($countUnread as $_u) {
                if(isset($countArr[$_u['_to']]))
                    $countArr[$_u['_to']]['unread'] = $_u['count'];
            }

            $this->render('support', array('count' => $countArr, 'default' => $this->renderPartial('_messages', array(
                    'messages' => $this->_get('_all')), true)));
        }
    }

    public function action_get($act, $_g = '', $_s = '')
    {
        echo json_encode(array('html' => $this->renderPartial('_messages_list', array(
                'messages' => $this->_get($act, $_g, $_s)), true)));
        Yii::app()->end();
    }

    public function action_getMessage($act, $id)
    {
        $actions = array(
            '_all'     => array('moderator', 0),
            '_bugs'    => array('admin', 1),
            '_finance' => array('admin', 2),
            '_offers'  => array('admin', 3),
        );

        if(isset($actions[$act]) AND Yii::app()->user->checkAccess($actions[$act][0])) {
            $ticket = Tickets::model()->findByPk($id);
            $user_name = Yii::app()->db->createCommand("SELECT name FROM {{accounts}} WHERE id='" . $ticket->owner_id . "'")->queryScalar();
            $messages = Yii::app()->db->createCommand("SELECT * FROM {{tickets_messages}} WHERE ticket_id=:id ORDER BY id ASC")->queryAll(true, array(
                ':id' => $id));

            $support_users = Yii::app()->db->createCommand("SELECT id, name FROM {{accounts}} WHERE role='admin' OR role='moderator'")->queryAll();
            $_supports = array();

            foreach($support_users as $support) {
                $_supports[$support['id']] = $support['name'];
            }

            $_list = array();

            foreach($messages as $message) {
                if($message['ot_id']) {
                    $message['name'] = isset($_supports[$message['ot_id']]) ? $_supports[$message['ot_id']] : 'undefined';
                } else
                    $message['name'] = $user_name;

                $_list[] = $message;
            }

            if($ticket->admin_read == 0) {
                $ticket->admin_read = 1;

                $ticket->save();
            }

            echo json_encode(array('html' => $this->renderPartial('_messages_read_list', array(
                    'messages' => $_list, 'ticket' => $ticket), true)));
            Yii::app()->end();
        } else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
    }

    public function action_setImportance($id)
    {
        $value = (isset($_POST['e']) AND intval($_POST['e'])) ? $_POST['e'] : 0;
        $sth = Yii::app()->db->createCommand("UPDATE {{tickets}} SET importance=:value WHERE id=:id");
        $sth->execute(array(':id' => $id, ':value' => $value));
    }

    public function actionRemove($id)
    {
        $sth = Yii::app()->db->createCommand("UPDATE {{tickets}} SET _is_remove=1 WHERE id=:id");
        $rowCount = $sth->execute(array(':id' => $id));
        $code = $rowCount ? 200 : 0;

        echo json_encode(array('code' => $code));
        Yii::app()->end();
    }

    public function action_process($id)
    {
        $ticket = Tickets::model()->findByPk($id);

        if(count($ticket)) {
            if($ticket->_status != 1) {
                $sth = Yii::app()->db->createCommand("UPDATE {{tickets}} SET _status=1 WHERE id=:id");
                $rowCount = $sth->execute(array(':id' => $id));
                $code = $rowCount ? 200 : 0;
            } else {
                $sth = Yii::app()->db->createCommand("UPDATE {{tickets}} SET _status=2 WHERE id=:id");
                $rowCount = $sth->execute(array(':id' => $id));

                Yii::app()->db->createCommand("INSERT INTO {{tickets_messages}} (ticket_id,ot_id,_text,_date) VALUES (:ticket_id,:ot_id,:_text,:_date)")
                    ->execute(array(
                        ':ticket_id' => $id,
                        ':ot_id'     => Yii::app()->user->id,
                        ':_text'     => 'Ваш запрос, был успешно решён администратором. Проверьте его выполнение, и если всё в порядке закройте или удалите запрос.',
                        ':_date'     => date('Y-m-d H:i:s'),
                    ));

                if($ticket->user_read == 1) {
                    $user = Users::model()->findByPk($ticket->owner_id);
                    $user->saveCounters(array('mail_unread' => 1));
                }

                $ticket->_status = 2;
                $ticket->user_read = 0;
                $ticket->_reply = serialize(array('id'   => Yii::app()->user->id,
                                                  'name' => Yii::app()->user->_get('name')));
                $ticket->_date_last_answer = date('Y-m-d H:i:s');

                $ticket->save();

                $code = $rowCount ? 201 : 0;
            }

            echo json_encode(array('code' => $code));
            Yii::app()->end();
        } else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
    }

    private function _get($act, $status = '', $importance = '')
    {
        $actions = array(
            '_all'     => array('moderator', 0),
            '_bugs'    => array('admin', 1),
            '_finance' => array('admin', 2),
            '_offers'  => array('admin', 3),
        );

        if(isset($actions[$act]) AND Yii::app()->user->checkAccess($actions[$act][0])) {
            $where = array();

            $where[] = '_to=' . $actions[$act][1];

            switch($status) {
                case "process":
                    $where[] = '_status=1 AND _is_remove=0';
                    break;

                case "reply":
                    $where[] = '_status=2 AND _is_remove=0';
                    break;

                case "close":
                    $where[] = '_status=3 AND _is_remove=0';
                    break;

                case "remove":
                    $where[] = '_is_remove=1';
                    break;

                default:
                    $where[] = '_status=0 AND _is_remove=0';
            }

            switch($importance) {
                case "low":
                    $where[] = 'importance=3';
                    break;

                case "middle":
                    $where[] = 'importance=2';
                    break;

                case "important":
                    $where[] = 'importance=1';
                    break;

                default:
                    $where[] = 'importance=0';
            }

            $criteria = new CDbCriteria;
            $criteria->condition = implode(" AND ", $where);
            $criteria->order = '_date DESC';

            $pages = new CPagination(Tickets::model()->count($criteria));

            $pages->pageSize = 10;
            $pages->applyLimit($criteria);

            $messages = Tickets::model()->findAll($criteria);

            return $messages;
        } else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
    }

}
