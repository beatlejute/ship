<?php

namespace abstracts;


interface orderInterface {

    function getCost($fromCity, $fromRegion, $toCity, $toRegion, $toPoint, $index, $length, $depth, $width, $weight, $count, $declaredValue, $invoiceNumber); //Расчёт стоимости доставки
    function create($orders); //Создание заказа на доставку
    function edit($orderNumber, $invoiceNumber, $recipientName, $recipientPhone, $recipientEmail, $declaredValue, $toPoint, $addressIndex, $addressCountry, $addressRegion, $addressCity, $addressStreet, $addressBuilding, $addressHousing, $addressApartment, $addressPorch, $addressFloor, $addressInfo); //Изменение заказа
    function getInfo($orderNumber, $invoiceNumber); //Отслеживание статуса и информация о доставке
    function cancel($orderNumber, $invoiceNumber); //Отмена заказа

}