<?php

class DefaultController extends Controller
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
                'actions' => array('index', 'filters'),
                'roles'   => array('moderator'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionIndex($id = 0, $act = '')
    {
        switch($act) {
            case "remove":
                Yii::app()->db->createCommand("DELETE FROM {{to_do}} WHERE id=:id")->execute(array(':id' => $id));
                break;

            case "change":
                $s = Yii::app()->db->createCommand("SELECT * FROM {{to_do}} WHERE id=:id")->queryRow(true, array(':id' => $id));

                if($s['_is_finished'] == 1) {
                    Yii::app()->db->createCommand("UPDATE {{to_do}} SET _is_finished=0")->execute(array(':id' => $id));
                    $status = 0;
                } else {
                    Yii::app()->db->createCommand("UPDATE {{to_do}} SET _is_finished=1 WHERE id=:id")->execute(array(':id' => $id));
                    $status = 1;
                }
                break;

            case "add":
                $text = isset($_POST['text']) ? $_POST['text'] : false;

                if($text) {
                    Yii::app()->db->createCommand("INSERT INTO {{to_do}} (owner_id,_text,_date) VALUES (:owner_id,:_text,:_date)")->execute(array(':_date' => time(), ':_text' => $text, ':owner_id' => Yii::app()->user->id));
                } else {
                    echo json_encode(array('code' => 201));
                    Yii::app()->end();
                }
                break;
        }

        $do_list = Yii::app()->db->createCommand("SELECT * FROM {{to_do}} ORDER BY _date DESC")->queryAll();

        if(Yii::app()->request->isAjaxRequest) {
            echo json_encode(array('code' => 200, 'status' => $status, 'html' => $this->renderPartial('_do_list', array('do_list' => $do_list), true)));
            Yii::app()->end();
        } else {
            $this->render('index', array('do_list' => $do_list));
        }
    }

    public function actionFilters()
    {
        if(isset($_GET['domen'])) {
            $domen = CHelper::_getDomen($_GET['domen']);
            $msg = isset($_GET['reason']) ? $_GET['reason'] : 'violations of service';

            Yii::app()->redis->hSet('twitter:filters:domain', $domen, $msg);
        }

        $domens = Yii::app()->redis->hGetAll('twitter:filters:domain');

        foreach($domens as $domen => $reason) {
            echo $domen . '- ' . $reason . "<br>";
        }
    }
}