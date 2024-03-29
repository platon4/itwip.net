<?php

namespace twitter\models\orders\status;

use Yii;
use twitter\models\tweets\methods\Fast;

class Orders extends \FormModel
{
    public $limit;
    protected $_pages;
    protected $_rows;
    protected $_count;
    protected $limits = [
        10 => ['value' => 10, 'title' => 10],
        20 => ['value' => 20, 'title' => 20],
        30 => ['value' => 30, 'title' => 30],
        40 => ['value' => 40, 'title' => 40],
        50 => ['value' => 50, 'title' => 50]
    ];
    protected $_times;

    public function rules()
    {
        return [
            ['limit', 'in', 'range' => array_keys($this->limits), 'message' => 'Неправильное количество заказов на странице.']
        ];
    }

    public function getCount()
    {
        if($this->_count === null) {
            $this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_orders}} WHERE owner_id=:owner")->queryScalar([':owner' => Yii::app()->user->id]);
        }

        if($this->_count <= 0)
            $this->addError('order', 'К сожалению, мы не смогли найти запрашиваемый вами заказ.');

        return $this->_count;
    }

    public function getRows()
    {
        if($this->_rows === null) {
            $db = Yii::app()->db;
            $rows = [];

            $orders = $db->createCommand("SELECT o.*,
                                                (SELECT COUNT(*) FROM {{twitter_ordersPerform}} WHERE order_hash=o.order_hash AND IF(o.type_order='manual', status>1, status>0)) as completed_taks,
                                                (SELECT COUNT(*) FROM {{twitter_ordersPerform}}  WHERE order_hash=o.order_hash) as all_taks,
                                                (SELECT SUM(return_amount) FROM {{twitter_ordersPerform}} WHERE order_hash=o.order_hash AND IF(o.type_order='manual', status=3, status=2)) as amount_use,
                                                (SELECT SUM(return_amount) FROM {{twitter_ordersPerform}} WHERE order_hash=o.order_hash) as return_amount FROM {{twitter_orders}} o WHERE o.owner_id=:owner ORDER BY id DESC LIMIT " . $this->getPages()->getOffset() . ", " . $this->getPages()->getLimit())->queryAll(true, [':owner' => Yii::app()->user->id]);

            foreach($orders as $order) {
                $order['params'] = json_decode($order['_params'], true);
                unset($order['_params']);
                $rows[] = $order;
            }

            $this->_rows = $rows;
        }

        return $this->_rows;
    }

    public function getPages()
    {
        if($this->_pages === null) {
            $this->_pages = new \CPagination($this->getCount());
            $this->_pages->pageSize = 10;
        }

        return $this->_pages;
    }

    public function getLimits()
    {
        return $this->limits;
    }

    public function getTime($t)
    {
        if($this->_times === null) {
            $times = new Fast();

            $this->_times = [];
            foreach($times->dropdown as $k => $v) {
                $this->_times[$k] = $v['time'];
            }
        }

        return isset($this->_times[$t]) ? Yii::t('twitterModule.orders', '{n} час | {n} часа | {n} часов', $this->_times[$t]) : 'не определено';
    }

    public function getCompleteProcent($complete, $all)
    {
        if($complete == 0 || $all == 0)
            return 0;

        return round(($complete / $all) * 100, 2);
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getView()
    {
        return Yii::app()->request->isAjaxRequest ? 'status/_indexRows' : 'status/index';
    }
}