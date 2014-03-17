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

    public function rules()
    {
        return [
            ['id_str', 'integer'],
            ['owner_id', 'integer']
        ];
    }

    public function afterValidate()
    {
        if (!$this->hasErrors()) {
            $this->run();
        }
    }

    public function run()
    {
        $count = (new Query())->from('{{%tw_accounts}}')->where(['id' => $this->id_str])->count();

        if ($count)
            $this->updateAccount();
        else
            $this->newAccount();
    }

    protected function newAccount()
    {
        try {
            $t = Yii::$app->db->beginTransaction();
            $oauth = new oAuth();
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

            $t->commit();
        } catch (\Exception $e) {
            $this->rollBack();
        }
    }

    protected function updateAccount()
    {
        echo 'update';
        die();
    }
} 