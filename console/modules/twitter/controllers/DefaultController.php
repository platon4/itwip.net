<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Query;
use common\helpers\String;

/*
 * php /var/www/itwip/cmd twitter (Interval 5 min)
 */

class DefaultController extends \console\components\Controller
{
    public function actionIndex()
    {
        $this->launchTweetingDaemons();
    }

    /**
     * Берем список приложений, и запускаем демоног под них
     */
    protected function launchTweetingDaemons()
    {
        Yii::$app->redis->set('console:twitter:tweeting', 'true', 30);

        sleep(rand(35, 60));

        $rows = (new Query())
            ->select('daemon')
            ->from('{{%twitter_tweeting}}')
            ->groupBy(['daemon'])
            ->all();

        if(!empty($rows)) {
            $path = realpath(Yii::$app->getBasePath() . '/..');
            $runtime = Yii::$app->getRuntimePath() . "/daemon";

            if(!is_dir($runtime)) {
                @mkdir($runtime, 0777);
                @chmod($runtime, 0777);
            }

            foreach($rows as $row) {
                $daemon = $row['daemon'];
                $log = $runtime . "/daemon_log_" . $daemon . ".txt";

                exec("nohup php $path/cmd twitter/tweeting " . $daemon . " > $log 2>&1 &");
            }
        }
    }
} 