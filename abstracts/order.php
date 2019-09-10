<?php

namespace abstracts;


abstract class order implements orderInterface {

    use orderTrait;

    Protected $authorization;
    Protected $memcache;

    public function __construct($authorization, $memcache=false)
    {
        $this->authorization = $authorization;
        $this->memcache = $memcache;
    }

    abstract public function getCost($fromCity, $fromRegion, $toCity, $toRegion, $toPoint, $index, $length, $depth, $width, $weight, $count, $declaredValue, $invoiceNumber);
    abstract public function create($orders);
    abstract public function edit($orderNumber, $invoiceNumber, $recipientName, $recipientPhone, $recipientEmail, $declaredValue, $toPoint, $addressIndex, $addressCountry, $addressRegion, $addressCity, $addressStreet, $addressBuilding, $addressHousing, $addressApartment, $addressPorch, $addressFloor, $addressInfo);
    abstract public function getInfo($orderNumber, $invoiceNumber);
    abstract public function cancel($orderNumber, $invoiceNumber);

}