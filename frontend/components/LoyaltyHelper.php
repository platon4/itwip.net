<?php

class LoyaltyHelper {

    static protected $loyalt       =null;
    static protected $referral_data=array(
        0=>array(40,'0-99'),
        1=>array(41,'100-199'),
        2=>array(42,'200-299'),
        3=>array(43,'300-399'),
        4=>array(44,'400-499'),
        5=>array(45,'500-599'),
        6=>array(46,'600-699'),
        7=>array(47,'700-799'),
        8=>array(48,'800-899'),
        9=>array(49,'900-999'),
        10=>array(50,'1500'),
        11=>array(51,'2500'),
    );
    static protected $finance_data =array(
        0=>array(15,0),
        1=>array(14,5000),
        2=>array(13,10000),
        3=>array(12,15000),
        4=>array(11,25000),
        5=>array(10,40000),
        6=>array(9,80000),
        7=>array(8,1600000),
        8=>array(7,3200000),
        9=>array(6,6400000),
        10=>array(5,12000000),
        11=>array(3,24000000),
    );

    public static function _referral($k)
    {
        return isset(self::$referral_data[$k])?self::$referral_data[$k][0]:self::$referral_data[0][0];
    }

    public static function _finance($k)
    {
        return isset(self::$finance_data[$k][0])?self::$finance_data[$k][0]:self::$finance_data[0][0];
    }

    public static function _getData($t)
    {
        return $t == 'referral'?self::$referral_data:self::$finance_data;
    }

    public static function _getPrecent($t,$id=0)
    {
        $loyalt=self::_getData($t);

        if(Yii::app()->params['extract_precent_system'] == 'on')
        {
            if($t == 'referral')
                $l=self::_getLoyalt('loyalty_referral',$id);
            else
                $l=self::_getLoyalt('loyalty_finance',$id);
        } else
            return 0;

        return isset($loyalt[$l][0])?$loyalt[$l][0]:0;
    }

    public static function _getLoyalt($key,$id)
    {
         if(!$id)
            $id=Yii::app()->user->id;
         
        if(self::$loyalt === NULL)
        {
            self::$loyalt=Yii::app()->db->createCommand("SELECT * FROM {{loyalty}} WHERE owner_id=:id")->queryRow(true,array(
                ':id'=>$id));
        }

        return isset(self::$loyalt[$key])?self::$loyalt[$key]:'';
    }

}
