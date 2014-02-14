<?php

/**
 * Description of Referrals
 *
 * @author Александр
 */
class Referrals extends FormModel
{

    protected $pages;
    protected $params;
    protected $count;
    public $sort;
    public $order = 'DESC';

    public function rules()
    {
        return array(
            array('sort', 'in', 'range' => array('date', 'last_visit', 'in', 'out', 'balance', 'bonus', 'tw_count')),
            array('order', 'in', 'range' => array('ASC', 'DESC')),
        );
    }

    public function _getRows()
    {
        $this->pages = new CPagination($this->_getCount());
        $this->pages->pageSize = 25;

        $sql = "SELECT a.id,a._date_create,a._date_last_visit,a.money_amount,a.bonus_money,l.in_balance,l.out_balance, (SELECT COUNT(*) FROM {{tw_accounts}} WHERE owner_id=a.id AND _status=1) as tw_accounts FROM {{accounts}} a INNER JOIN {{loyalty}} l ON a.id=l.owner_id";

        if (count($this->_getParams()))
            $sql.=" WHERE " . implode(" AND ", $this->_getParams());

        if ($this->sort !== NULL)
        {
            $sorts = array(
                'date' => 'a._date_create',
                'last_visit' => 'a._date_last_visit',
                'in' => 'l.in_balance',
                'out' => 'l.out_balance',
                'tw_count' => 'tw_accounts',
                'bonus' => 'a.bonus_money',
                'balance' => 'a.money_amount',
            );

            if ($sorts[$this->sort])
                $sql.=" ORDER BY " . $sorts[$this->sort] . " " . $this->order;
        }
        else
            $sql.=" ORDER BY a._date_create " . $this->order;

        $sql.=" LIMIT " . $this->pages->getOffset() . "," . $this->pages->getLimit();

        $referrals = Yii::app()->db->createCommand($sql)->queryAll(true,$this->_getValues());

        $rows = array();
        foreach ($referrals as $row)
        {
            $row['_price'] = $this->_getPrice($row['money_amount'], $row['in_balance'], $row['out_balance'], $row['tw_accounts']);
            $rows[] = $row;
        }

        return $rows;
    }

    public function _getParams()
    {
        return array(
            'l.parent_referral=0',
            'a.id!=:id',
            '((a.money_amount!=0 AND a.bonus_money!=500 AND a.bonus_money!=0) OR (SELECT COUNT(*) FROM {{tw_accounts}} WHERE owner_id=a.id AND _status=1))',
            'a._date_last_visit>\'' . date('Y-m-d H:i:s', time() - (7 * 86400)) . '\''
        );
    }

    public function _getValues()
    {
        return array(
            ':id' => Yii::app()->user->id
        );
    }

    public function _getCount()
    {
        if ($this->count === null)
        {
            $sql = "SELECT COUNT(*) FROM {{accounts}} a INNER JOIN {{loyalty}} l ON a.id=l.owner_id";

            if (count($this->_getParams()))
                $sql.=" WHERE " . implode(" AND ", $this->_getParams());

            $this->count = Yii::app()->db->createCommand($sql)->queryScalar($this->_getValues());
        }

        return $this->count;
    }

    public function _getPages()
    {
        return $this->pages;
    }

    private function _getPrice($money_amount, $in_balance, $out_balance, $tw_accounts)
    {
        $money_am = ($money_amount - $in_balance) > 0 ? $money_amount : 0;

        return ceil(100 + ($in_balance * 7) + ($money_am * 7) + ($out_balance * 7) + ($tw_accounts * 15));
    }
}
