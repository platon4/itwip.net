<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Command;
use yii\db\Query;
use console\components\Daemon;

/*
 * php cmd twitter/tweeting
 */

class TweetingController extends \console\components\Controller
{
    protected $apps;
    protected $daemon;

    public function actionIndex($daemon)
    {
        /* проверяем если домен не запущен уже */
        if(!Daemon::isRunning('tweeting_' . $this->daemon)) {
            $this->daemon = $daemon;
            $redis = Yii::$app->redis;

            Daemon::setProcess('tweeting_' . $this->daemon);

            /* Запускаем демона */
            while(true) {
                /* проверяем если твиттинг не остановлен */
                if($redis->exists('console:twitter:tweeting') === false) {
                    if(Daemon::isSetProcess('tweeting_' . $this->daemon)) {
                        $this->reloadApps();

                        $tasks = (new Query())->from('{{%twitter_tweeting}}')->limit(10)->all();

                        if(!empty($tasks)) {
                            foreach($tasks as $task) {

                            }
                        } else {
                            sleep(5);
                        }
                    } else {
                        echo "Daemon won't start, error set process\n";
                        exit(0);
                    }
                } else {
                    echo "Deaemon is stopped\n";
                    exit(0);
                }
                /* делаем перерыв на 1 секунду */
                sleep(rand(1, 5));
            }
        } else {
            echo "Daemon is already running\n";
            exit(-1);
        }
    }

    /**
     * Перезагружаем приложения
     */
    protected function reloadApps()
    {
        if(Yii::$app->redis->exists('console:twitter:tweeting:reload') === true) {
            $this->apps = null;
            Yii::$app->redis->delete('console:twitter:tweeting:reload');
        }
    }

    /**
     * Получаем список приложений для запущенного демона
     *
     * @param $key
     * @return bool
     */
    protected function appGet($key)
    {
        if($this->apps === null) {
            $rows = (new Query())->from('{{%twitter_apps}}')->where(['daemon' => $this->daemon])->all();

            if(!empty($rows)) {
                foreach($rows as $row) {
                    $this->apps[$row['id']] = $row;
                }
            }
        }

        return isset($this->apps[$key]) ? $this->apps[$key] : false;
    }
}