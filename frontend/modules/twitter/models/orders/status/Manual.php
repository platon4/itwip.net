<?php

namespace twitter\models\orders\status;

use Yii;
use twitter\components\Twitter;

class Manual extends \FormModel
{
    public $h;
    public $limit = 10;
    public $_o = 'status';
    public $_a = 'DESC';
    protected $_pages;
    protected $_rows;
    protected $_order;
    protected $_count;
    protected $limits = [
        10 => ['value' => 10, 'title' => 10],
        20 => ['value' => 20, 'title' => 20],
        30 => ['value' => 30, 'title' => 30],
        40 => ['value' => 40, 'title' => 40],
        50 => ['value' => 50, 'title' => 50]
    ];
    protected $orders = [
        'status' => 'status'
    ];

    public function rules()
    {
        return [
            ['h', 'ext.validators.hashValidator', 'min' => 7, 'max' => 20],
            ['limit', 'in', 'range' => array_keys($this->limits), 'message' => 'Неправильное количество элементов на странице.'],
            ['_o', 'in', 'range' => array_keys($this->orders)],
            ['_a', 'in', 'range' => ['ASC', 'DESC']],
            ['_count', 'getCount']
        ];
    }

    public function afterValidate()
    {
        if($this->getOrder() === false)
            $this->addError('order', 'Извините, но такого заказа нету.');

        if($this->_o === '')
            $this->_o = 'status';

        if($this->_a === '')
            $this->_a = 'ASC';
    }

    public function getCount()
    {
        if($this->_count === null) {
            $this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_ordersPerform}} WHERE order_hash=:hash")->queryScalar([':hash' => $this->h]);
        }

        if($this->_count <= 0)
            $this->addError('order', 'К сожалению, мы не смогли найти запрашиваемый вами заказ.');

        return $this->_count;
    }

    public function getRows()
    {
        if($this->_rows === null) {
            $order = $this->orders[$this->_o] . ' ' . ($this->_a == 'ASC' ? 'ASC' : 'DESC');

            $orders = Yii::app()->db->createCommand("SELECT id,cost,return_amount,tweet, status,posted_date,_params,message,tw_account FROM {{twitter_ordersPerform}} WHERE order_hash=:hash ORDER BY " . $order . " LIMIT " . $this->getPages()->getOffset() . ", " . $this->getPages()->getLimit())->queryAll(true, [':hash' => $this->h]);
            $rows = [];
            $ids = [];
            $params = [];

            foreach($orders as $v) {
                $params[$v['id']] = json_decode($v['_params'], true);
                $ids[] = $v['tw_account'];
            }

            $accounts = Twitter::accounts($ids)->getAll(); //Загружаем данные выбраных аккаунтов

            foreach($orders as $order) {
                $account = $accounts[$order['tw_account']];
                $rows[] = [
                    'avatar'       => $account['avatar'],
                    'name'         => $account['name'],
                    'screen_name'  => $account['screen_name'],
                    'tweet'        => $order['tweet'],
                    'message'      => $order['message'],
                    'id'           => $order['id'],
                    'status'       => $order['status'],
                    'amount'       => $order['return_amount'],
                    'payment_type' => $this->getOrder()['payment_type'],
                    'placed_date'  => date('d.m.Y H:i', strtotime($order['posted_date'])),
                    'params'       => $params[$order['id']],
                    'message'      => $order['message']
                ];
            }

            $this->_rows = $rows;
        }

        return $this->_rows;
    }

    public function getOrder()
    {
        if($this->_order === null) {
            $this->_order = Yii::app()->db->createCommand("SELECT * FROM {{twitter_orders}} WHERE order_hash=:hash AND owner_id=:owner AND type_order='manual'")->queryRow(true, [':owner' => Yii::app()->user->id, ':hash' => $this->h]);
        }

        return $this->_order;
    }

    public function getPages()
    {
        if($this->_pages === null) {
            $this->_pages = new \CPagination($this->getCount());
            $this->_pages->pageSize = $this->getLimit();
        }

        return $this->_pages;
    }

    public function getLimits()
    {
        return $this->limits;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getView()
    {
        if(Yii::app()->request->isAjaxRequest)
            return 'status/_mDetailsRows';
        else
            return 'status/_mDetails';
    }
}