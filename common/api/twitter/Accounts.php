<?php

namespace common\api\twitter;

use yii\db\Query;

class Accounts
{
    protected $query;

    public function __construct()
    {
        $this->query = (new Query())->from('{{%tw_accounts}} a')->innerJoin('{{%tw_accounts_settings}} s', 'a.id=s.tid');
    }

    /*
     * Выбор аккаунтов по индексам
     */
    public function where($condition, $params = [])
    {
        $this->query->where($condition, $params);

        return $this;
    }

    public function all()
    {
        return $this->query->all();
    }

    public function one()
    {
        return $this->query->one();
    }

    /*
     * Обновление данных аккаунтов
     */
    public function update($condition, $params = [])
    {

    }
}