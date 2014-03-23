<?php

namespace console\modules\twitter\components;

trait ErrorsTreit
{
    public function unknownError($model)
    {

    }

    /**
     * Выполняем при ошибки с кодом 188 (твит содержит вредусную ссылку, твиттер отверг твит)
     */
    public function removeMalwareTweet($model)
    {

    }

    /**
     * Выполняем при ошибки с кодом 89 (Нету доступа к аккаунту)
     */
    public function InvalidExpiredToken($model)
    {

    }

    /**
     * Выполняем при ошибки с кодом 64 (Учетная запись временно приостановлена)
     */
    public function accountIsSuspended($model)
    {

    }
} 