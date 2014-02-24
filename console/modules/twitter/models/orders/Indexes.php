<?php

namespace console\modules\twitter\models\orders;

class Indexes implements  OrdersInterface
{
    use OrdersTrait;

    /*
     * Обработка заказа
     */
    public  function create(array $data)
    {
        return [];
    }
}