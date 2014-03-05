<?php

namespace console\modules\twitter\models;

interface OrdersInterface
{
	public function process(array $data);

    public function make();

    public function clear();
} 