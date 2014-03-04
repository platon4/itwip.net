<?php

/**
 * Description of Buy
 *
 * @author Александр
 */
class Buy extends FormModel {

    public $price;
    public $id;
    protected $ref;
    protected $count;

    public function rules()
    {
        return array(
            array('id','numerical','integerOnly'=>true,'allowEmpty'=>false),
            array('price','numerical','allowEmpty'=>false),
            array('price','_price'),
        );
    }

    public function _price()
    {
        $row=Yii::app()->db->createCommand("SELECT a._date_create,a._date_last_visit,a.email,a.money_amount,a.bonus_money,l.owner_id,l.in_balance,l.out_balance,l.parent_referral, (SELECT COUNT(*) FROM {{tw_accounts}} WHERE owner_id=a.id AND _status=1) as tw_accounts FROM {{accounts}} a INNER JOIN {{loyalty}} l ON a.id=l.owner_id WHERE a.id=:id")->queryRow(true,array(
            ':id'=>$this->id));

        if($row !== false)
        {
            if($row['parent_referral'] != 0)
            {
                $this->addError('price',Yii::t('shopModule.index','_invalid_buy_referral'));
            } else
            {
                if($this->_getPrice($row['money_amount'],$row['in_balance'],$row['out_balance'],$row['tw_accounts']) != $this->price)
                {
                    $this->addError('price',Yii::t('shopModule.index','invalid_price'));
                }
                else {
                    if($row['owner_id']== Yii::app()->user->id)
                        $this->addError('price',Yii::t('shopModule.index','impossible_by_account'));
                }
                    
            }

            $this->ref=$row['email'];
        } else
        {
            $this->addError('price',Yii::t('shopModule.index','no_exist_referral'));
        }

    }

    public function process()
    {
        try
        {
            $t=Yii::app()->db->beginTransaction();

            if(Finance::payment($this->price,Yii::app()->user->id,1,4,'',$this->ref))
            {
                Yii::app()->db->createCommand("UPDATE {{loyalty}} SET parent_referral=:ref WHERE owner_id=:id")->execute(array(
                    ':id'=>$this->id,':ref'=>Yii::app()->user->id));

                $t->commit(); //Сохранить транзакцию
                return true;
            } else
            {
                $this->addError('price',Yii::t('shopModule.index','invalid_pay_action',array(
                            '{price}'=>Finance::money($this->price,1,true))));
                $t->rollBack(); //Отменяем транзакцию
                return false;
            }
        } catch(Exception $ex)
        {
            $this->addError('price',$ex);
            $t->rollBack(); //Отменяем транзакцию
            return false;
        }
    }

    public function _getParams()
    {
        return array(
            'l.parent_referral=0',
            '(a.money_amount!=0 AND a.bonus_money!=500 AND a.bonus_money!=0)',
            'a._date_last_visit>\''.date('Y-m-d H:i:s',time() - (7 * 86400)).'\''
        );
    }

    public function _getCount()
    {
        if($this->count === null)
        {
            $sql="SELECT COUNT(*) FROM {{accounts}} a INNER JOIN {{loyalty}} l ON a.id=l.owner_id";

            if(count($this->_getParams()))
                $sql.=" WHERE ".implode(" AND ",$this->_getParams());

            $this->count=Yii::app()->db->createCommand($sql)->queryScalar();
        }


        return $this->count;
    }

    private function _getPrice($money_amount,$in_balance,$out_balance,$tw_accounts)
    {
        $money_am=($money_amount - $in_balance) > 0?$money_amount:0;

        return ceil(100 + ($in_balance * 7) + ($money_am * 7) + ($out_balance * 7) + ($tw_accounts * 15));
    }

}
