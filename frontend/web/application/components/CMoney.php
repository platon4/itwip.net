<?php

class CMoney {

    static $_data;

    public static function blockingType($t,$id=0)
    {
        return 'в разработке';
    }

    public static function _reportNotice($t,$m,$s,$id=0)
    {
       $data=array(
                0=>array(
                    'bonus'=>Yii::t('main','_s_promo_code'),
                    'purse'=>array(1=>Yii::t('main','_s_webmoney'),2=>Yii::t('main','_s_robokassa')),
                    'referral'=>Yii::t('financeModule.index','income_log_form_ref'),

                ),
                1=>array(
                    'twitter'=>Yii::t('financeModule.index','twitter_money_order',array('{id}'=>$id)),
                ),              
       );    

        if(isset($data[$t][$m]))
        {
            if(is_array($data[$t][$m]))
            {
                return isset($data[$t][$m][$s])?$data[$t][$m][$s]:Yii::t('financeModule.index','_undefined_log');
            }
            else
                return $data[$t][$m];          
        }
        else
            return Yii::t('financeModule.index','_undefined_log');
    }

    public static function _c($summ,$cy=false,$bill=0)
    {
        $prefix="";
        $bill  =($bill)?$bill:Yii::app()->user->_setting('_preferred_currency');

        $moneyData=self::getData();

        if($bill AND isset($moneyData[$bill - 1]))
        {
            $summ  =round(($moneyData[$bill - 1]['_calc'] == 0)?$summ / $moneyData[$bill - 1]['course']:$summ * $moneyData[$bill - 1]['course'],2);
            $prefix=Yii::t("internal","_money_".$moneyData[$bill - 1]['_vlt']);
        } else
        {
            $prefix=Yii::t("internal","_money_0");
        }

        $summ=(0 + $summ);

        if($cy)
        {
            $summ=$summ." ".$prefix;
        }

        return $summ;
    }

    public static function convert($summ,$bill=0)
    {
        $bill     =($bill)?$bill:Yii::app()->user->_setting('_preferred_currency');
        $moneyData=self::getData();

        if($bill AND isset($moneyData[$bill - 1]))
        {
            $summ=round(($moneyData[$bill - 1]['_calc'] == 0)?$summ * $moneyData[$bill - 1]['course']:$summ / $moneyData[$bill - 1]['course'],2);
        }

        return $summ;
    }

    public static function _systemPay($s)
    {
        $sys=array(
            '_s_promo_code',
            '_s_webmoney',
            '_s_robokassa',
        );

        return isset($sys[$s])?Yii::t('main',$sys[$s]):Yii::t('main',$sys[0]);
    }

    public static function itrCost($itr)
    {
        $_itr=0.50;
        $cost=0.10;

        $s=1;

        for($i=1; $i <= $itr; $i=$i + 0.1)
        {
            if($i>1)
            {
                $_itr+=$cost;

                if($s < floor($i))
                {
                    $cost+=0.10;
                    $s++;
                }
            }
        }

        return $_itr;
    }

    protected static function getData()
    {
        if(self::$_data === null)
        {
            $cache=Yii::app()->cache->get('_it_money_course');
            if($cache)
                $cache=unserialize($cache);
            if($cache)
                return $cache;

            self::$_data=Yii::app()->db->createCommand("SELECT * FROM it_money_course")->queryAll();

            Yii::app()->cache->set('_it_money_course',serialize(self::$_data));
        }

        return self::$_data;
    }

    public function CalcOutSumm($summ)
    {
        $request=CHelper::_getURL('https://merchant.roboxchange.com/WebService/Service.asmx/CalcOutSumm?MerchantLogin=itwip&IncCurrLabel=WMRM&IncSum='.$summ);

        if($request['code'] == 200)
        {
            $xml=new SimpleXMLElement($request['response']);

            return round((string)$xml->OutSum,2);
        } else
            return false;

        return 0;
    }

    public function _extractPrecent($amount,$m,$uid=0)
    {
        if(intval($uid))
            $id=$uid;
        
            $precent=($m == 'referral')?LoyaltyHelper::_getPrecent('referral',$id):LoyaltyHelper::_getPrecent('finance',$id);
            $amount =$amount - (($amount * $precent) / 100);

        return array('precent'=>$precent,'amount'=>$amount);
    }

    public function _outStatus($s)
    {
        return '';
    }
}
