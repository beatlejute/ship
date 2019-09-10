<?php

namespace abstracts;

interface infoInterface { // справочники и информация

    function methodsList(); //Получение списка методов
    //Нормализация адреса
    //Нормализация ФИО
    //Нормализация телефона
    //Получение списка регионов
    //Получение списка районов
    function cityList(); //Получение списка городов
    //Получение списка улиц
    //Определение индекса

    function parcelShopsList($cityName, $weight, $size, $payment); //Получение списка точек самовывоза
    function shippingPointsList(); //Получение списка точек сдачи
    function tariffList(); //Информация о тарифах
    function errorList(); //Информация об ошибках
    function statusList(); //Информация об статусах

}