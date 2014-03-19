<?php

namespace twitter\models\accounts;

use Yii;
use yii\base\Exception;

class Accounts extends \ActiveRecord
{
    public $tid;
    public $agreed;
    public $act;
    public $status;

    protected $_updates;
    protected $_url;
    protected $_data;
    protected $_message;
    protected $_code = 200;
    protected $_filter;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{tw_accounts}}';
    }

    public function relations()
    {
        return [
            'settings' => [self::HAS_ONE, 'twitter\models\accounts\Settings', 'tid']
        ];
    }

    public function rules()
    {
        return [
            ['tid', 'numerical', 'integerOnly' => true, 'allowEmpty' => false, 'message' => Yii::t('yii', 'Your request is invalid.'), 'except' => 'add'],
            ['tid', 'validateExists', 'except' => 'add'],
            ['status', 'in', 'range' => ['on', 'off'], 'on' => 'status'],
            ['agreed', 'compare', 'compareValue' => '1', 'message' => Yii::t('twitterModule.accounts', '_twitterAccountAdd_agreed'), 'on' => 'add'],
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function afterValidate()
    {
        Yii::app()->redis->set('twitter:accounts:twitter:is_update:' . Yii::app()->user->id . ':' . $this->get('id'), 'true');
        $methods = [
            'record' => 'updateRecord',
            'save'   => 'saveSettings',
            'remove' => 'removeAccount',
            'status' => 'changeStatus',
            'data'   => 'dataCollection'
        ];

        if(isset($methods[$this->getScenario()]) && method_exists($this, $methods[$this->getScenario()])) {
            $this->$methods[$this->getScenario()]();
        }
    }

    public function validateExists()
    {
        if($this->get('id') === null) {
            $this->addError('tid', 'Аккаунта не найден, возможно он был удален, или у вас недостаточно прав.');
        } else if($this->get('owner_id') != Yii::app()->user->id) {
            $this->addError('tid', 'Вы не являетесь владельцем данного аккаунта, доступ запрещен.');
        }
    }

    public function authorize()
    {
        $app = $this->getApp();

        if($app !== false) {
            $this->_url = $app['ip'] . '/twitter/oauth/authorize?token=' . $app['token'];
            return true;
        } else {
            return false;
        }
    }

    /**
     * Генерируем ссылку для редиректа
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return strpos('http://', $this->_url) !== false ? $this->_url : 'http://' . $this->_url;
    }

    /**
     * Проверяем последнее обновление данных аккаунта
     */
    public function lastUpdate($key)
    {
        if($this->_updates === null) {
            $this->_updates = Yii::app()->redis->hGetAll('twitter:accounts:updates:' . $this->tid);
        }

        return isset($this->_updates[$key]) && $this->_updates[$key] ? true : false;
    }

    public function getSubjects()
    {
        $subjects = \Html::groupByKey(\Subjects::model()->_getAll(['order' => 'sort']), 'id', '_key', 'parrent');

        $_subjects = explode(",", $this->get('_subjects', 'settings'));
        $_subject_html = "";

        if(count($_subjects)) {
            $i = 0;

            foreach($_subjects as $_id) {
                $_subject_html .= Yii::app()->controller->renderPartial('application.modules.twitter.views.default._subjectsDropDownList', ['remove' => (!$i) ? 0 : 1, 'selected' => $_id, 'bid' => '_subjects_0', 'subjects' => $subjects], true);

                foreach($subjects as $zs3q => $a3q2z) {
                    if($zs3q == $_id) {
                        unset($subjects[$zs3q]);
                    } else {
                        if(is_array($a3q2z)) {
                            foreach($a3q2z as $u3n6 => $bb3q) {
                                foreach($bb3q as $ak => $av) {
                                    if($ak == $_id)
                                        unset($subjects[$zs3q][$u3n6][$ak]);
                                }
                            }
                        }
                    }
                }

                $i++;
            }
        } else {
            $_subject_html = Yii::app()->controller->renderPartial('application.modules.twitter.views.default._subjectsDropDownList', ['selected' => 0, 'bid' => '_subjects_0', 'subjects' => $subjects], true);
        }

        return $_subject_html;
    }

    public function getAges()
    {
        return require Yii::app()->getModulePath() . '/twitter/data/_age.php';
    }

    public function getFilter($key)
    {
        if($this->_filter === null)
            $this->_filter = explode(',', $this->get('_stop', 'settings'));

        return is_array($this->_filter) && in_array($key, $this->_filter) ? 1 : 0;
    }

    public function get($key = '', $with = '')
    {
        if($this->_data === null) {
            $this->_data = $this->with('settings')->findByPk($this->tid);
        }

        if(trim($with) != '' && $this->_data !== null) {
            return isset($this->_data->$with->$key) ? $this->_data->$with->$key : ($key != '' ? null : $this->_data->$with);
        } else
            return isset($this->_data->$key) ? $this->_data->$key : ($key != '' ? null : $this->_data);
    }

    /**
     * Возврощаем сообщение
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    public function getCode()
    {
        return $this->_code;
    }

    protected function updateRecord()
    {
        $timeout = Yii::app()->redis->get('twitter:accounts:add:timeout:' . $this->get('id'));

        if($timeout === false) {
            $app = $this->getApp();

            if($app !== false) {
                $this->_code = 301;
                $this->_url = $app['ip'] . '/twitter/oauth/update?token=' . $app['token'];
            } else {
                $this->_message = 'В данный момент обновление недоступно, пожалуйста попробуйте позже.';
            }
        } else {
            $this->_message = 'Обновление невозможно, обновлять данные аккаунта можно раз в час, осталось ' . (60 - floor((time() - $timeout) / 60)) . ' мин.';
        }
    }

    protected function removeAccount()
    {
        if(Yii::app()->user->id == $this->get('owner_id')) {
            try {
                $t = Yii::app()->db->beginTransaction();

                Yii::app()->db->createCommand("DELETE FROM {{tw_accounts}} WHERE id=" . $this->tid)->execute();
                Yii::app()->db->createCommand("DELETE FROM {{tw_accounts_settings}} WHERE tid=" . $this->tid)->execute();
                Yii::app()->db->createCommand("DELETE FROM {{tw_accounts_stats}} WHERE tw_id=" . $this->tid)->execute();

                Yii::app()->user->setFlash('accountsMessages', 'Аккаунта "' . \Html::encode($this->get('screen_name')) . '" успешно удален.');

                $this->_code = 301;
                $t->commit();
            } catch(Exception $e) {
                $this->_message = 'Не удалось удалить аккаунт, системная ошибка, обратитесь в поддержку.';
                $t->rollBack();
            }
        } else {
            $this->_message = 'Вы не являетесь владельцем данного аккаунта, действие заблокировано.';
        }
    }

    public function changeStatus()
    {
        if(in_array($this->get('_status'), [1, 7])) {
            $this->get()->_status = $this->status == 'on' ? 1 : 7;
            $this->get()->save(false);
        } else {
            $this->addError('status', 'Вы не можете вкл/выкл. данный аккаунт.');
        }
    }

    public function status($n = 0, $notice = '')
    {
        if($n === 0)
            $n = $this->get('_status');

        $status = [
            0  => '_status_0', //Модерация
            1  => '_status_1', //Работает
            2  => '_status_2', //недопушен
            3  => '_status_3', //забанен
            4  => '_status_4', //нет доступа
            5  => '_status_5', //
            6  => '_status_6', //Не соотвествует требованьям
            7  => '_status_7', //Отключен
            15 => '_status_4', //нет доступа
        ];

        return (array_key_exists($n, $status)) ? Yii::t('main', $status[$n], ['{text}' => $notice]) : Yii::t('main', '_status_undefined');
    }

    protected function saveSettings()
    {
        $model = $this->get('', 'settings');

        if(isset($_POST['subject']) && isset($_POST['Settings']))
            $_POST['Settings']['subject'] = $_POST['subject'];

        if(isset($_POST['Settings']))
            $model->load($_POST['Settings'], true);

        if($model->validate()) {
            Yii::app()->user->setFlash('_settings_save_success', 'Настройки успешно сохранены.');
            $model->save();
        } else {
            Yii::app()->user->setFlash('tw_settings_message', ['type' => 'error', 'text' => $model->getError()]);
        }
    }

    /**
     * Выбераем приложения подходяшие для добавление аккаунта.
     */
    protected function getApp()
    {
        if(in_array($this->getScenario(), ['record']))
            $app = Yii::app()->db->createCommand("SELECT * FROM {{twitter_apps}} a WHERE(id =:id AND is_active = 1) OR ((SELECT COUNT(*) FROM {{tw_accounts}} WHERE _status = 1 AND a . id = app) < :count AND is_active = 1) LIMIT 1")->queryRow(true, [':count' => Yii::app()->params['twitter']['accountsInApp'], ':id' => $this->get('app')]);
        else
            $app = Yii::app()->db->createCommand("SELECT * FROM {{twitter_apps}} a WHERE is_active = 1 AND (SELECT COUNT(*) FROM {{tw_accounts}} WHERE _status = 1 AND a . id = app) < :count ORDER BY RAND() LIMIT 1")->queryRow(true, [':count' => Yii::app()->params['twitter']['accountsInApp']]);

        if($app !== false) {
            $token = \CHelper::_gString(8, 15);

            Yii::app()->redis->set('twitter:accounts:auth:' . $token, json_encode([
                'owner_id'    => Yii::app()->user->id,
                'app'         => $app['id'],
                'hash'        => md5(\CHelper::_getIP() . Yii::app()->user->id . $this->tid . $app['id'] . $token),
                'account_id'  => $this->tid,
                'user_key'    => $this->get('_key'),
                'user_secret' => $this->get('_secret'),
                'app_key'     => $app['_key'],
                'app_secret'  => $app['_secret'],
                'app_ip'      => $app['ip']
            ]));

            Yii::app()->redis->expire('twitter:accounts:auth:' . $token, 10 * 60);
            $app['token'] = $token;

            return $app;
        } else {
            return false;
        }
    }

    protected function dataCollection()
    {
        $token = \CHelper::_gString(8, 15);

        Yii::app()->redis->set('twitter:accounts:data:' . $token, json_encode([
            'owner_id'   => Yii::app()->user->id,
            'hash'       => md5(\CHelper::_getIP() . Yii::app()->user->id . $this->tid . $token),
            'account_id' => $this->tid
        ]));

        $request = \CHelper::_getURL('http://195.242.161.92/twitter/accounts/get?token=' . $token . '&act=data&_c[]=yandexRank&_c[]=yandexRobot&_c[]=googlePR');

        if($request['code'] == 200) {
            $response = json_decode($request['response'], true);

            if($response['code'] == 200) {
                $this->addError('tid', $request['response']);
            } else {
                $this->addError('tid', $response['message']);
            }
        } else {
            $this->addError('tid', $request['response']);
            //$this->addError('tid', 'Не удалось получить ответ сервера, попробуйте перезагрузить страницу, если ошибка повторится, обратитесь в службу поддержки.');
        }
    }

    /**
     * @return bool
     */
    public function updateProcess()
    {
        if(Yii::app()->redis->exists('twitter:accounts:twitter:is_update:' . Yii::app()->user->id . ':' . $this->get('id'))) {

            Yii::app()->redis->delete('twitter:accounts:twitter:is_update:' . Yii::app()->user->id . ':' . $this->get('id'));

            return true;
        } else {
            return false;
        }
    }
}