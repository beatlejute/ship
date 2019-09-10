<?php

namespace abstracts;


trait returnsTrait {

    public function createListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function createListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }


    public function getReturnsListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function getReturnsListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }


    public function getInfoPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if($dateFrom) {

            if(!$dateTo) $dateTo = date();
            $request = $this->getReturnsList($dateFrom, $dateTo);

            foreach($request->SendingsInfo as $order) {

                $orderList[] = $order->InvoiceNumber;

            }

            if($orders) $orders = array_intersect($orders, $orderList);
            else $orders = $orderList;

        }


        return get_defined_vars();

    }
    public function getInfoPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }

}