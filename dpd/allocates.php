<?php

namespace dpd;

class allocates extends \abstracts\allocates {

    public function courier($FIO, $Phone, $Date, $TimeStart='', $TimeEnd='', $City='', $Address='', $Number='', $Weight='', $ordersDateFrom='', $ordersDateTo='') { //Вызов курьера для отгрузки

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;



        return $response['error'] = 'Служба dpd не поддерживает вызов курьера для отгрузки, вызов курьера оформляется в методе order/create!';



        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

    }
    public function cancel($allocateNumber) { //Отмена отгрузки

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;



        return $response['error'] = 'Служба dpd не поддерживает отмену отгрузки!';



        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

    }

}
