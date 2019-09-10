<?php

namespace abstracts;


abstract class allocates implements allocatesInterface {

    Protected $authorization;
    Protected $memcache;

    use allocatesTrait;

    public function __construct($authorization, $memcache=false)
    {
        $this->authorization = $authorization;
        $this->memcache = $memcache;
    }

    abstract public function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo=''); //Вызов курьера
    abstract public function cancel($allocateNumber); //Отмена отгрузки

}