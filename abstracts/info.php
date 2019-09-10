<?php

namespace abstracts;

abstract class info implements infoInterface {

    Protected $authorization;
    Protected $memcache;

    use infoTrait;

    public function __construct($authorization, $memcache=false)
    {
        $this->authorization = $authorization;
        $this->memcache = $memcache;
    }

    abstract public function methodsList();
    abstract public function cityList();
    abstract public function parcelShopsList($cityName, $weight, $payment, $declaredValue);
    abstract public function shippingPointsList();
    abstract public function tariffList();
    abstract public function errorList();
    abstract public function statusList();

}

?>