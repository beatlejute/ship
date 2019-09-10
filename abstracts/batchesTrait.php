<?php

namespace abstracts;


trait batchesTrait
{

    public function getOrderListPreProcessing($get_defined_vars) {

        extract($get_defined_vars);




        return get_defined_vars();

    }
    public function getOrderListPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        foreach($response['Invoices'] as $orderId => $order) if(!$order['CustomerNumber']) {

            unset($response['Invoices'][$orderId]);
            $errorInfo[] = 'Получено отправление без номера: ' . print_r($order, true);

        }

        if(!$response['Invoices']) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось получить данные ни одного отправления.";
            if(isset($errorInfo)) $answer['errors']['error']['info'] = $errorInfo;

            return $answer;

        }

        $response['Invoices'] = array_values($response['Invoices']);


        return get_defined_vars();

    }
    
    
    
    public function getLabelPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$putdata && !$dateFrom) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Не указан номер отправления.";

            return $answer;

        }

        $orders = $putdata;

        if($dateFrom) {

            if(!$dateTo) $dateTo = date();

            $request = $this->getOrderList($dateFrom, $dateTo);

            foreach($request['Invoices'] as $order) {

                $orderList[] = $order['Number'];

            }

            if($orders) $orders = array_intersect($orders, $orderList);
            else $orders = $orderList;

        }


        return get_defined_vars();

    }
    public function getLabelPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$response) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось получить данные ни одного отправления.";
            if(isset($errorInfo)) $answer['errors']['error']['info'] = $errorInfo;

            return $answer;

        }

        header('Content-type: application/pdf');


        return get_defined_vars();

    }



    public function createPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        $orders = $putdata;

        if($dateFrom) {

            if(!$dateTo) $dateTo = date();

            $request = $this->getOrderList($dateFrom, $dateTo);

            foreach($request['Invoices'] as $order) {

                $orderList[] = $order['Number'];

            }

            if($orders) $orders = array_intersect($orders, $orderList);
            else $orders = $orderList;

        }


        return get_defined_vars();

    }
    public function createPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }



    public function getInfoPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function getInfoPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        header('Content-type: application/pdf');


        return get_defined_vars();

    }



    public function removeOrderPreProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }
    public function removeOrderPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        header('Content-type: application/pdf');


        return get_defined_vars();

    }

}