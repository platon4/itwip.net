<?php

namespace twitter\models\orders\status;

use Yii;

class Indexes extends \FormModel
{
    public $h;
    public $limit = 10;
    protected $_pages;
    protected $_rows;
    protected $_order;
    protected $limits = [
        10 => ['value' => 10, 'title' => 10],
        20 => ['value' => 20, 'title' => 20],
        30 => ['value' => 30, 'title' => 30],
        40 => ['value' => 40, 'title' => 40],
        50 => ['value' => 50, 'title' => 50]
    ];

    protected $times = [
        5  => 43200,
        10 => 32400,
        20 => 21600,
        40 => 10800,
        65 => 3600,
    ];

    protected $_count;

    public function rules()
    {
        return [
            ['h', 'ext.validators.hashValidator', 'min' => 7, 'max' => 20],
            ['limit', 'in', 'range' => array_keys($this->limits), 'message' => 'Неправильное количество заказов на странице.'],
            ['_count', 'getCount']
        ];
    }

    public function getCount()
    {
        if($this->_count === null)
            $this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_ordersPerform}} WHERE order_hash=:hash")->queryScalar([':hash' => $this->h]);

        if($this->_count <= 0)
            $this->addError('order', 'К сожалению, мы не смогли найти запрашиваемый вами заказ.');

        return $this->_count;
    }

    public function getRows()
    {
        if($this->_rows === null) {
            $orders = Yii::app()->db->createCommand("SELECT id,url,cost,return_amount,status,_params,message,posted_date FROM {{twitter_ordersPerform}} WHERE order_hash=:hash LIMIT " . $this->getPages()->getOffset() . ", " . $this->getPages()->getLimit())->queryAll(true, [':hash' => $this->h]);
            $rows = [];

            foreach($orders as $order) {
                $params = json_decode($order['_params'], true);
                $post_date = strtotime($order['posted_date']);

                $rows[] = [
                    'id'          => $order['id'],
                    'url'         => $order['url'],
                    'posted_date' => ($order['posted_date'] == '0000-00-00 00:00:00' ? '-' : date('d.m.Y H:i', $post_date)),
                    'check_date'  => ($order['posted_date'] == '0000-00-00 00:00:00' ? '-' : date('d.m.Y H:i', $post_date + $this->times[$params['time']])),
                    'status'      => $order['status'],
                    'yandex_url'  => 'http://yandex.ru/yandsearch?text=' . urlencode('url:' . $order['url']),
                    'params'      => $params,
                ];

                unset($order);
            }

            $this->_rows = $rows;
        }

        return $this->_rows;
    }

    public function getOrder()
    {
        if($this->_order === null) {
            $this->_order = Yii::app()->db->createCommand("SELECT * FROM {{twitter_orders}} WHERE order_hash=:hash AND owner_id=:owner AND type_order='indexes'")->queryRow(true, [':owner' => Yii::app()->user->id, ':hash' => $this->h]);
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
            return 'status/_fDetailsRows';
        else
            return 'status/_fDetails';
    }
}