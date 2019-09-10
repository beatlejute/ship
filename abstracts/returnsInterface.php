<?php

namespace abstracts;


interface returnsInterface {

    function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum=''); //Регистрация возврата
    function getReturnsList($dateFrom, $dateTo); //Получение списка возвратных отправлений
    function getInfo($orders='', $dateFrom='', $dateTo='');//Получение информации по возвратным отправлениям
    //Получение акта возврата денег
    //Получение акта возврата товара

}