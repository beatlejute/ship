<?php

namespace easyway;

class allocates extends \abstracts\allocates {

    public function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo='') { //Вызов курьера для отгрузки

        return $response['error'] = 'Служба EasyWay не поддерживает вызов курьера!';

    }
    public function cancel($allocateNumber) { //Отмена отгрузки

        return $response['error'] = 'Служба EasyWay не поддерживает вызов и отмену курьера!';

    }

}