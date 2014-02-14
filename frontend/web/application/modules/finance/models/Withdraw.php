<?php

/**
 * Description of Withdraw
 *
 * @author Александр
 */
class Withdraw extends FormModel {

    public $_pb    =0;
    public $_purse;
    public $_amount=0;
    public $_timeout;
    
    public function rules()
    {
        return array(
            array('_amount,_pb','numerical','numberPattern'=>'/^[0-9]{1,4}(\.[0-9]{0,2})?$/',
                'message'=>'{attribute} имеет неправильный формат.'),
            array('_pb','_moneyAmount'),
            array('_purse','required'),
            array('_purse','match','pattern'=>'/R[0-9]{12}/','message'=>'Неправильный формат R кошелька.'),
            array('_timeout','_timeout'),            
        );
    }

    public function afterValidate()
    {
        if(!$this->hasErrors())
        {
            $this->createOrder();
        }
    }

    public function attributeLabels()
    {
        return array(
            '_pb'=>'Сумма зачисление',
            'amount'=>'Сумма списания',
            '_purse'=>'Кошелёк',
        );
    }

    public function _moneyAmount()
    {
        if($this->_pb > 0)
        {
            if($this->_pb > Yii::app()->user->_get('money_amount'))
            {
                $this->addError('_pb','Указанная сумма больше чем есть на балансе.');
            } else if($this->_pb < 10)
            {
                $this->addError('_pb','Минимальная сумма для вывода 10 руб.');
            }
        } else
        {
            $this->addError('_pb','Укажите сумму для вывода.');
        }
    }

    public function _timeout()
    {
        $time=Yii::app()->db->createCommand("SELECT _date,_time FROM {{money_withdrawal}} WHERE owner_id=:id ORDER BY id DESC LIMIT 1")->queryRow(true,array(
            ':id'=>Yii::app()->user->id));

        if($time !== false)
        {
            $_time=strtotime($time['_date'].' '.$time['_time']);

            if($_time > (time() - 86400))
                $this->addError('_pb','После подачи заявки прошло менее 24 часов.');
        }
    }

    protected function createOrder()
    {
        try
        {
            $t=Yii::app()->db->beginTransaction();

            $params=array(':owner_id'=>Yii::app()->user->id,
                ':_date'=>date('Y-m-d'),':_time'=>date('H:i:s'));

            $_p=LoyaltyHelper::_getPrecent('finance');

            if($this->_amount - ($this->_amount * $_p / 100) == $this->_pb)
            {
                $comission=$this->_amount * $_p / 100;
                $extract_amount=$this->_amount;
                $amount=$this->_pb;
            } else
            {
                $comission=$this->_pb * $_p / 100;
                $extract_amount=$this->_pb+$comission;
                $amount=$this->_pb;
            }

            $params[':_out']       =$amount;
            $params[':_commission']=$comission;

            if($amount <= Yii::app()->params['autoEjectSumm'])
                $params[':_status']=1;
            else
                $params[':_status']=0;

            $params[':amount']=$extract_amount;

            Yii::app()->db->createCommand("UPDATE {{accounts}} SET money_amount=money_amount-:money WHERE id=:id")->execute(array(
                ':id'=>Yii::app()->user->id,':money'=>$extract_amount));

            Yii::app()->db->createCommand("INSERT INTO {{money_withdrawal}} (owner_id,amount,_commission,_out,_date,_time,_status) VALUES (:owner_id,:amount,:_commission,:_out,:_date,:_time,:_status)")->execute($params);

            $oID=Yii::app()->db->getLastInsertId();
            Yii::app()->db->createCommand("INSERT INTO {{money_blocking}} (owner_id,amount,_date,_type,_for,_id,_money_type,_notice) VALUES (:owner_id,:amount,:_date,:_type,:_for,:_id,:_money_type,:_notice)")->execute(array(
                ':owner_id'=>Yii::app()->user->id,':amount'=>$extract_amount,':_date'=>date('Y-m-d H:i:s'),
                ':_type'=>1,':_for'=>1,':_id'=>$oID,':_money_type'=>0,':_notice'=>''));

            Finance::_setMoneyLog($extract_amount,Yii::app()->user->id,0,1,1,1,$oID,'',1);
            $t->commit();
        } catch(Exception $e)
        {
            echo $e;
            $t->rollBack();
        }
    }

}
