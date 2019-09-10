<?php

namespace abstracts;


abstract class returns implements returnsInterface {

    Protected $authorization;
    Protected $memcache;

    use returnsTrait;

    public function __construct($authorization, $memcache=false)
    {
        $this->authorization = $authorization;
        $this->memcache = $memcache;
    }

    abstract public function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum=''); //Регистрация возврата
    abstract public function getReturnsList($dateFrom, $dateTo); //Получение списка возвратных отправлений
    abstract public function getInfo($orders='', $dateFrom='', $dateTo='');//Получение информации по возвратным отправлениям

}

?>