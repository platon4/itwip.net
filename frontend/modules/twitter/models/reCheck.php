<?php

/**
 * Description of reCheck
 *
 * @author eolitich
 */
class reCheck extends FormModel
{

    public $id;
    protected $_load;
    public $_data;

    public function rules()
    {
        return array(
            array('id', 'numerical', 'integerOnly' => true),
            array('id', '_status'),
            array('id', '_recheck'),
        );
    }

    public function beforeValidate()
    {
        $this->addError('id', 'Обновление данных временно недоступно. Приносим извенения за неудобства.');
    }

    public function afterValidate()
    {
        $this->_load();
    }

    public function _status()
    {
        $this->_load();

        if($this->_load['_status'] != 6 AND !Yii::app()->user->checkAccess('admin')) {
            $this->addError('id', Yii::t('twitterModule.accounts', '_invalid_recheck_status'));
        }
    }

    public function _recheck()
    {
        $this->_load();
        $app = Yii::app()->params['twitter']['app'][$this->_load['app']];

        $tmh = new tmhOAuth(array('consumer_key'    => $app['app_key'],
                                  'consumer_secret' => $app['app_secret']));

        $tmh->config['user_token'] = $this->_load['_key'];
        $tmh->config['user_secret'] = $this->_load['_secret'];

        $code = $tmh->request('GET', $tmh->url('1.1/account/verify_credentials.json?skip_status=false'));

        if($code == 200) {
            $this->_data = json_decode($tmh->response['response']);

            if($this->_data->followers_count < 500) {
                $this->addError('id', Yii::t('id', Yii::t('twitterModule.accounts', 'small_followers_no_check')));
            } else {
                $this->accountUpdate();
            }
        } else {
            if($code == 401) {
                $this->addError('id', Yii::t('id', Yii::t('twitterModule.accounts', 'no_recheck_access')));
                Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET _status=4 WHERE id=:id")->execute(array(':id' => $this->id));
            } else {
                $this->addError('id', Yii::t('id', Yii::t('twitterModule.accounts', 'no_data_from_twitter')));
            }
        }
    }

    public function accountUpdate()
    {
        $itr = THelper::itr($this->_data->statuses_count, $this->_data->followers_count, date("d.m.Y", $this->_load['created_at']), $this->_load['listed_count'], $this->_load['yandex_rank'], $this->_load['google_pr'], $this->_load['_mdr']);

        $fields = array(
            '_status=1',
            'itr = :itr',
            'followers=:followers',
            'tweets=:tweets',
            'listed_count=:listed_count',
            'screen_name=:screen_name',
            'name=:name',
            'avatar=:avatar',
            'following=:following',
        );

        $values = array(
            'itr'           => $itr,
            ':id'           => $this->id,
            ':avatar'       => $this->_data->profile_image_url,
            ':following'    => $this->_data->friends_count,
            ':tweets'       => $this->_data->statuses_count,
            ':name'         => $this->_data->name,
            ':listed_count' => $this->_data->listed_count,
            ':screen_name'  => $this->_data->screen_name,
            ':followers'    => $this->_data->followers_count,
        );

        Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET " . implode(", ", $fields) . " WHERE id=:id")->execute($values);
    }

    public function _load()
    {
        if($this->_load === null) {
            $this->_load = Yii::app()->db->createCommand("SELECT * FROM {{tw_accounts}} WHERE id=:id")->queryRow(true, array(':id' => $this->id));
        }

        return $this->_load;
    }
}
