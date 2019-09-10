<?php

namespace abstracts;


abstract class batches implements batchesInterface {

    Protected $authorization;
    Protected $memcache;

    use batchesTrait;

    public function __construct($authorization, $memcache=false)
    {
        $this->authorization = $authorization;
        $this->memcache = $memcache;
    }

    abstract public function getOrderList($dateFrom, $dateTo);
    abstract public function getLabel($orders='', $dateFrom='', $dateTo='');
    abstract public function create($orders='', $dateFrom='', $dateTo='');
    abstract public function getInfo($invoiceNumber='', $reestrNumber='');
    abstract public function removeOrder($orderNumber='', $invoiceNumber='');

}