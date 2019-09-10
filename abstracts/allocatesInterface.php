<?php

namespace abstracts;


interface allocatesInterface {

    function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo=''); //Вызов курьера
    function cancel($allocateNumber); //Отмена отгрузки

}