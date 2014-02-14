<?php

#* * * * * php -e /var/www/itwip.net/application/cron.php twitter CheckingTweets
#*/2 * * * * php -e /var/www/itwip.net/application/cron.php twitter UpdateTwAccounts

class TwitterCommand extends CConsoleCommand {

    /**
     * function UpdateTwAccounts
     * Обновление данных твитер аккаунтов
     */
    public function actionUpdateTwAccounts()
    {
        @ini_set('max_execution_time',110);
        @set_time_limit();

        $accounts     =Yii::app()->db->createCommand("SELECT s.id as sid,s.tw_id,s._data, t.id,t.followers,t.tweets,t.listed_count,t.yandex_rank,t.google_pr,t.created_at,t._mdr FROM {{tw_accounts_stats}} s INNER JOIN {{tw_accounts}} t ON s.tw_id=t.id WHERE s.is_update=0 AND s._date='".date("Y-m-d",time() - 86400)."' LIMIT 50")->queryAll();
        $update_fields=array(
            'followers_count'=>'followers',
            'statuses_count'=>'tweets',
            'friends_count'=>'following',
            'listed_count'=>'listed_count',
            'screen_name'=>'screen_name',
            'name'=>'name',
            'profile_image_url'=>'avatar',
            'lang'=>'_lang',
            'in_google'=>'in_google',
            'google_pr'=>'google_pr',
            'yandex_rank'=>'yandex_rank',
            'in_yandex'=>'in_yandex',
        );

        foreach($accounts as $row)
        {
            $data               =unserialize($row['_data']);
            $updates            =array();
            $params             =array();

            foreach($data as $k=> $v)
            {
                if(array_key_exists($k,$update_fields))
                {
                    $updates[]                     =$update_fields[$k].'=:'.$update_fields[$k];
                    $params[':'.$update_fields[$k]]=$v;

                    if(array_key_exists($update_fields[$k],$row))
                    {
                        if(trim($v) != '')
                            $row[$update_fields[$k]]=$v;
                    }
                }
            }

            if(count($updates))
            {
                $updates[]     ='itr=:itr';
                $params[':itr']=THelper::itr($row['tweets'],$row['followers'],date("d.m.Y",$row['created_at']),$row['listed_count'],$row['yandex_rank'],$row['google_pr'],$row['_mdr']);

                $params[':id']=$row['tw_id'];
                Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET ".implode(", ",$updates)." WHERE id=:id")->execute($params);
                $is_update    =1;
            } else
                $is_update=2;

            Yii::app()->db->createCommand("UPDATE {{tw_accounts_stats}} SET is_update=:is WHERE id=:id")->execute(array(
                ':id'=>$row['sid'],':is'=>$is_update));
        }
    }

    /**
     * function CheckingTweets
     * Обновление данных твитер аккаунтов
     */
    public function actionCheckingTweets()
    {
        @ini_set('max_execution_time',50);
        @set_time_limit();

        $tweetCount=0;

        $tweets=Yii::app()->db->createCommand("SELECT tw.id,tw.owner_id,tw._cost,tw.tw_id,tw._text,tw.pay_type,tw.ot_id,a.screen_name,a.id as aid FROM {{tw_tweets}} tw INNER JOIN {{tw_accounts}} a ON tw.tid=a.id WHERE tw._status=0 AND tw._date<'".date("Y-m-d H:i:s",time() - (3 * 86400))."' ORDER BY tw._date ASC LIMIT 5")->queryAll();

        if($tweets !== false)
        {
            foreach($tweets as $tweet)
            {
                $b=Yii::app()->db->createCommand("SELECT id FROM {{money_blocking}} WHERE _id=:id LIMIT 1")->queryRow(true,array(
                    ':id'=>$tweet['tw_id']));

                try
                {
                    $t   =Yii::app()->db->beginTransaction();
                    $code=$this->tweetExists($tweet['aid'].'/status/'.$tweet['tw_id']);

                    if($code == 200)
                    {
                        if($tweet['pay_type'] == 0)
                            $m='money_amount';
                        else
                            $m='bonus_money';

                        Yii::app()->db->createCommand("UPDATE {{accounts}} SET {$m}={$m}+:money WHERE id=:id")->execute(array(
                            ':money'=>$tweet['_cost'],':id'=>$tweet['owner_id']));

                        Yii::app()->db->createCommand("INSERT INTO {{money_logs}} (owner_id,_type,_system,_date,_time,_amount,is_blocked,order_id,amount_type,_notice,_transfer) VALUES (:owner_id,0,5,:_date,:_time,:_amount,:is_blocked,:order_id,:amount_type,:_notice,:_transfer)")
                                ->execute(array(
                                    ':owner_id'=>$tweet['owner_id'],
                                    ':_date'=>date("Y-m-d"),
                                    ':_time'=>date("H:i:s"),
                                    ':_amount'=>$tweet['_cost'],
                                    ':is_blocked'=>0,
                                    ':order_id'=>$tweet['tw_id'],
                                    ':amount_type'=>$tweet['pay_type'],
                                    ':_notice'=>$tweet['screen_name'],
                                    ':_transfer'=>2
                        ));

                        Yii::app()->db->createCommand("UPDATE {{tw_tweets}} SET _status=1 WHERE id=:id")->execute(array(
                            ':id'=>$tweet['id']));

                        Yii::app()->db->createCommand("DELETE FROM {{money_blocking}} WHERE id=:id")->execute(array(
                            ':id'=>$b['id']));

                        Logs::save("checking","Date: ".date('d.m.Y H:i:s').";tweet: https://twitter.com/".$tweet['screen_name']."/status/".$tweet['tw_id']."\n",'twitter','a+');
                    } elseif($code == 404)
                    {
                        Yii::app()->db->createCommand("INSERT INTO {{money_logs}} (owner_id,_type,_system,_date,_time,_amount,is_blocked,order_id,amount_type,_notice,_transfer) VALUES (:owner_id,1,3,:_date,:_time,:_amount,:is_blocked,:order_id,:amount_type,:_notice,:_transfer)")
                                ->execute(array(
                                    ':owner_id'=>$tweet['owner_id'],
                                    ':_date'=>date("Y-m-d"),
                                    ':_time'=>date("H:i:s"),
                                    ':_amount'=>$tweet['_cost'],
                                    ':is_blocked'=>0,
                                    ':order_id'=>$tweet['tw_id'],
                                    ':amount_type'=>$tweet['pay_type'],
                                    ':_notice'=>$tweet['screen_name'],
                                    ':_transfer'=>3
                        ));

                        Yii::app()->db->createCommand("INSERT INTO {{money_logs}} (owner_id,_type,_system,_date,_time,_amount,is_blocked,order_id,amount_type,_notice,_transfer) VALUES (:owner_id,2,3,:_date,:_time,:_amount,:is_blocked,:order_id,:amount_type,:_notice,:_transfer)")
                                ->execute(array(
                                    ':owner_id'=>$tweet['ot_id'],
                                    ':_date'=>date("Y-m-d"),
                                    ':_time'=>date("H:i:s"),
                                    ':_amount'=>$tweet['_cost'],
                                    ':is_blocked'=>0,
                                    ':order_id'=>$tweet['tw_id'],
                                    ':amount_type'=>$tweet['pay_type'],
                                    ':_notice'=>$tweet['screen_name'],
                                    ':_transfer'=>0
                        ));

                        Yii::app()->db->createCommand("UPDATE {{accounts}} SET bonus_money=bonus_money+:money WHERE id=:id")->execute(array(
                            ':money'=>$tweet['_cost'],':id'=>$tweet['ot_id']));

                        Yii::app()->db->createCommand("UPDATE {{tw_tweets}} SET _status=2 WHERE id=:id")->execute(array(
                            ':id'=>$tweet['id']));
                        Yii::app()->db->createCommand("DELETE FROM {{money_blocking}} WHERE id=:id")->execute(array(
                            ':id'=>$b['id']));

                        Logs::save("remove","Date: ".date('d.m.Y H:i:s').";tweet: https://twitter.com/".$tweet['screen_name']."/status/".$tweet['tw_id']."\n",'twitter','a+');
                    } else
                        Logs::save("unknow","Date: ".date('d.m.Y H:i:s').";tweet: https://twitter.com/".$tweet['screen_name']."/status/".$tweet['tw_id']."\n",'twitter','a+');

                    echo $code;
                    $t->commit();
                } catch(Exception $e)
                {
                    Logs::save("error-".md5(date("d-m-Y H:i:s").$tweet['tw_id']),$e,'twitter');
                    $t->rollBack();
                }

                $tweetCount++;
                sleep(1);
            }
        }

        echo "Done: ".$tweetCount."\n";
    }

    public function actionTest()
    {
        //print_r(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tweets_to_twitter}} t WHERE status=1 AND (SELECT COUNT(*) FROM {{tweets_to_twitter}} WHERE _order=t._order)=0")->queryAll());
    }

    protected function tweetExists($url)
    {
        $request=CHelper::_getURL('https://twitter.com/'.$url,'GET',array(),array(),true);

        return $request['code'];
    }

}
