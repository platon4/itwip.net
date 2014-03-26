<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Command;
use yii\db\Query;
use console\components\Daemon;
use console\modules\twitter\components\Tweeting;
use console\components\Logger;

/*
 * php cmd twitter/tweeting
 */

class TweetingController extends \console\components\Controller
{
    protected $apps;
    protected $daemon;
    protected $_dname;

    public function actionIndex($daemon)
    {
        $this->daemon = $daemon;

        /* проверяем если домен не запущен уже */
        if(!Daemon::isRunning($this->getDaemoName())) {
            $redis = Yii::$app->redis;

            Daemon::setProcess($this->getDaemoName());

            $tweeting = new Tweeting();

            /* Запускаем демона */
            while(true) {
                $this->message('Demonion start of the cycle');

                /* проверяем если твиттинг не остановлен */
                if($redis->exists('console:twitter:tweeting') === false) {
                    if(Daemon::isSetProcess($this->getDaemoName())) {
                        echo "-------------------------- Query ---------------------------" . PHP_EOL;
                        $where = ['and', 'daemon=:daemon']; // , 'process_time<=:time'

                        if($_task = $this->getExcludes('twitter:twitting:timeout:accounts', 'tw_account'))
                            $where[] = $_task;

                        if($_task = $this->getExcludes('console:twitter:tweeting:exclude:tweet', 'tweet_hash'))
                            $where[] = $_task;

                        if($_task = $this->getExcludes('console:twitter:tweeting:exclude:domen', 'domen'))
                            $where[] = $_task;

                        if($_task = $this->getExcludes('console:twitter:tweeting:exclude:id', 'order_id'))
                            $where[] = $_task;

                        $tasks = (new Query())
                            ->from('{{%twitter_tweeting}}')
                            ->where($where, [':daemon' => $this->daemon]) //, ':time' => date('H:i:s')
                            ->groupBy('domen')
                            ->limit(5)
                            ->all();
                        echo "-------------------------- End Query ---------------------------" . PHP_EOL;

                        if(!empty($tasks)) {
                            foreach($tasks as $task) {
                                echo "-------------------------- Run Task ---------------------------" . PHP_EOL;
                                Yii::$app->redis->set('orders:in_process:0:' . $task['order_id'], $task['order_id'], 5 * 60);
                                Yii::$app->redis->set('orders:in_process:1:' . $task['sbuorder_id'], $task['order_id'], 5 * 60);

                                $tweeting->processTask($task);

                                Yii::$app->redis->delete(['orders:in_process:0:' . $task['order_id'], 'orders:in_process:1:' . $task['sbuorder_id']]);
                                echo "--------------------------- Run timeout task --------------------------" . PHP_EOL;
                                sleep(rand(7, 15));
                            }
                        } else {
                            $this->message('Daemon timeout 5 sec.');
                            sleep(rand(5, 10));
                        }
                    } else {
                        Daemon::stopDaemon($this->daemon, 0, 'Daemon won\'t start, error set process');
                    }
                } else {
                    Daemon::stopDaemon($this->daemon, 0, 'Deaemon is stopped');
                }

                $timeout = rand(5, 10);
                $this->message('Demonion end of the cycle, timeout: ' . $timeout);

                /* делаем перерыв на 1 секунду */
                sleep($timeout);
            }
        } else {
            Daemon::stopDaemon($this->daemon, 0, 'Daemon is already running');
        }
    }

    protected function getExcludes($key, $field)
    {
        $rids = Yii::$app->redis->mGet(Yii::$app->redis->keys($key . ':*'));
        $where = null;

        if($rids !== false) {
            $ids = [];
            foreach($rids as $id) {
                if($id !== false)
                    $ids[] = $id;
            }

            if(!empty($ids)) {
                $where = ['not in', $field, $ids];
                echo $key . " exclude ids: " . count($ids) . " - " . implode(", ", $ids) . PHP_EOL;
            }
        }

        return !empty($where) ? $where : false;
    }

    protected function message($message)
    {
        $message = date('d.m.Y H:i:s') . ': ' . $message;
        echo $message . PHP_EOL;
        Logger::log($message, 'daemons/tweeting', 'daemon-' . $this->daemon);
    }

    protected function getDaemoName()
    {
        if($this->_dname === null)
            $this->_dname = md5(get_called_class()) . '_' . $this->daemon;

        return $this->_dname;
    }
}