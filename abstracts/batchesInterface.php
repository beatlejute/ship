<?php

namespace abstracts;


interface batchesInterface {

    function getOrderList($dateFrom, $dateTo); //Отбор списка заказов за временной интервал
    function getLabel($orders='', $dateFrom='', $dateTo=''); //Печать Наклеек
    function create(); //Создает партию
    function getInfo(); //Запрашивает данные об партиях
    function removeOrder(); //Исключение заказа на доставку из всех партий

}