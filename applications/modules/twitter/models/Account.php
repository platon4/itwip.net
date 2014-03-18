<?php

namespace app\modules\twitter\models;

use Yii;
use yii\db\Query;

class Account extends \app\components\Model
{
    public $id_str;
    public $owner_id;
    public $app;
    public $favourites_count;
    public $created_at;
    public $description;
    public $followers_count;
    public $friends_count;
    public $name;
    public $screen_name;
    public $lang;
    public $statuses_count;
    public $listed_count;
    public $profile_image_url;

    public $process_id;

    public function rules()
    {
        return [
            ['id_str', 'integer'],
            ['owner_id', 'integer'],
            [['process_id', 'app', 'created_at', 'favourites_count', 'listed_count', 'screen_name', 'name', 'lang', 'profile_image_url', 'statuses_count', 'friends_count'], 'safe'],
            ['followers_count', 'compare', 'operator' => '<=', 'compareValue' => Yii::$app->params['twitter']['minimuFollowers'], 'message' => 'Для участия в системе на вашем аккаунте не достаточно фолловеров (минимум ' . Yii::$app->params['twitter']['minimuFollowers'] . '). <p>Рекомендуем воспользоватся софтом для увелечения числа подписчиков - <a href="http://twidium.com">twidium.com</a></p>'],
            ['created_at', 'registrationDays'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['new'] = isset($scenarios['default']) ? $scenarios['default'] : [];

        return $scenarios;
    }

    public function afterValidate()
    {
        if (!$this->hasErrors()) {
            $this->run();
        }
    }

    public function run()
    {
        $row = (new Query())->from('{{%tw_accounts}}')->where(['id' => $this->id_str])->one();

        if ($row !== false) {
            if ($row['owner_id'] == $this->owner_id) {
                if ($this->getScenario() == 'new') {
                    $this->addError('error', 'Аккаунт "' . $this->screen_name . '" уже находится в системе, пожалуйста авторизуйтесь под аккаунтом который вы хотите добавить, и повторите действие.');
                } elseif ($this->id_str == $this->process_id) {
                    $this->updateAccount();
                } else {
                    $this->addError('error', 'Обновление невозможно, вы авторизованы под другтим аккаунтам.');
                }
            } else {
                $this->addError('error', 'Вы не можете обновить данные данного аккаунта, так как он привязан к другой учетной записи. Если Вы считаете, что это ошибка, обратитесь в службу поддержки с указанием логина твиттер аккаунта.');
            }
        } else {
            if ($this->getScenario() == 'new') {
                $this->newAccount();
            } else {
                $this->addError('error', 'Обновление невозможно, вы авторизованы под другтим аккаунтам.');
            }
        }
    }

    protected function newAccount()
    {
        try {
            $oauth = new \common\api\twitter\oAuth();
            $t = Yii::$app->db->beginTransaction();

            /* Добавление аккаунта */
            Yii::$app->db->createCommand()
                ->insert('{{%tw_accounts}}', [
                    'id' => $this->id_str,
                    '_key' => $oauth->get('access_token', $this->owner_id, 'oauth_token'),
                    '_secret' => $oauth->get('access_token', $this->owner_id, 'oauth_token_secret'),
                    'owner_id' => $this->owner_id,
                    'screen_name' => $this->screen_name,
                    'name' => $this->name,
                    'created_at' => strtotime($this->created_at),
                    'avatar' => $this->profile_image_url,
                    'app' => $this->app,
                    'date_add' => time(),
                    'tweets' => $this->statuses_count,
                    'following' => $this->friends_count,
                    'followers' => $this->followers_count,
                    'listed_count' => $this->listed_count,
                    '_lang' => $this->lang
                ])
                ->execute();

            /* Добавление настроек аккаунта */
            Yii::$app->db->createCommand()
                ->insert('{{%tw_accounts_settings}}', [
                    'tid' => $this->id_str
                ])
                ->execute();

            Yii::$app->redis->set('userFlash:twitter:accounts:' . $this->owner_id, 'Ваш аккаунт "@' . $this->screen_name . '" успешно добавлен в систему.', 60);
            Yii::$app->redis->set('twitter:accounts:twitter:is_update:' . $this->owner_id . ':' . $this->id_str, time());

            $t->commit();
        } catch (\Exception $e) {
            $this->addError('error', 'Не удалось добавить аккаунт, из-за системного сбоя. Обратитесь в службу поддержки.');
            $t->rollBack();
        }
    }

    protected function updateAccount()
    {
        try {
            $oauth = new \common\api\twitter\oAuth();

            $t = Yii::$app->db->beginTransaction();

            Yii::$app->db->createCommand()
                ->update('{{%tw_accounts}}', [
                    '_key' => $oauth->get('access_token', $this->owner_id, 'oauth_token'),
                    '_secret' => $oauth->get('access_token', $this->owner_id, 'oauth_token_secret'),
                    'screen_name' => $this->screen_name,
                    'name' => $this->name,
                    'avatar' => $this->profile_image_url,
                    'app' => $this->app,
                    'date_add' => time(),
                    'tweets' => $this->statuses_count,
                    'following' => $this->friends_count,
                    'followers' => $this->followers_count,
                    'listed_count' => $this->listed_count,
                    '_lang' => $this->lang
                ], ['id' => $this->id_str])
                ->execute();

            Yii::$app->redis->set('twitter:accounts:add:timeout:' . $this->id_str, time(), 60 * 60);
            Yii::$app->redis->set('userFlash:twitter:accounts:' . $this->owner_id, 'Данные аккаунта успешно обновлены.', 60);

            $t->commit();
        } catch (\Exception $e) {
            $this->addError('error', 'Не удалось лбновить данные аккаунта, из-за системного сбоя. Обратитесь в службу поддержки.');
            $t->rollBack();
        }
    }

    public function registrationDays($attribute)
    {
        if (ceil((time() - strtotime($this->created_at)) / 86400) < Yii::$app->params['twitter']['minimuRegistrationDays'])
            $this->addError($attribute, 'Для участия в системе, срок регистраций вашего аккаунта должен быть старше 1 месяца.');
    }
}