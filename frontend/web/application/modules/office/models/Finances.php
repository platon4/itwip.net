<?php

/**
 * Description of Finances
 *
 * @author eolitich
 */
class Finances
{

    public $data = array();

    public function getFinances()
    {
        if ($this->data === array())
        {
            $systemPay = array(0 => 'bonus', 1 => 'webmoney', 2 => 'robokassa');

            $income = Yii::app()->db->createCommand("SELECT SUM(amount) as _all, SUM(_add_to_balance) as to_balance,_system FROM {{money_replenishmentit}} WHERE is_pay=1 GROUP BY _system")->queryAll();

            foreach ($income as $row)
            {
                $this->data[$systemPay[$row['_system']]] = array(
                    '_all' => $row['_all'],
                    'to_balance' => $row['to_balance']
                );
            }

            $out = Yii::app()->db->createCommand("SELECT SUM(amount) as _all, SUM(_out) as _out FROM {{money_withdrawal}} WHERE _status=2")->queryRow();
            $this->data['out'] = array('_all' => $out['_all'], '_out' => $out['_out']);

            $user_amount = Yii::app()->db->createCommand("SELECT SUM(money_amount) as money, SUM(bonus_money) as bonus FROM {{accounts}} WHERE status=1")->queryRow();
            $user_amount_blocked = Yii::app()->db->createCommand("SELECT SUM(amount) as _sum,_money_type FROM {{money_blocking}} GROUP BY _money_type")->queryAll();

            $this->data['users'] = array(
                'money_amount' => $user_amount['money'],
                'money_bonus' => $user_amount['bonus'],
            );

            $moneyType = array(0 => 'money', 1 => 'bonus');

            foreach ($user_amount_blocked as $row)
                $this->data['users']['money_blocked_' . $moneyType[$row['_money_type']]] = $row['_sum'];

            $this->data['users']['referral'] = Yii::app()->db->createCommand("SELECT SUM(l.brought_user) as sum FROM {{loyalty}} l INNER JOIN {{accounts}} a ON l.owner_id=a.id WHERE a.status=1")->queryScalar();

            unset($income);
            unset($user_amount);
            unset($out);
            unset($user_amount_blocked);

            if (isset($row))
                unset($row);
        }

        return $this->data;
    }
}
