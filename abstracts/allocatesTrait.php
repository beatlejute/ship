<?php

namespace abstracts;


trait allocatesTrait {

    public function courierPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$FIO || !$Phone || !$Date) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Неполные входные данные.";

            return $answer;

        }

        if($dateFrom) {

            if(!$dateTo) $dateTo = date();

            $request = $batches->getOrderList($dateFrom, $dateTo);

            $Number = sizeof($request['Invoices']);

        }


        return get_defined_vars();

    }
    public function courierPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }


    public function cancelPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$allocateNumber) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Неполные входные данные.";

            return $answer;

        }


        return get_defined_vars();

    }
    public function cancelPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    
}