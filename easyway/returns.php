<?php

namespace easyway;

class returns extends \abstracts\returns {

    private $authorization;

    public function __construct($authorization)
    {
        $this->authorization = $authorization;
    }

    public function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum='') { //Регистрация возврата

        return $response['error'] = 'Служба EasyWay не поддерживает регистрацию возвратов!';

    }
    public function getReturnsList($dateFrom, $dateTo) { //Получение списка возвратных отправлений

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["DateFrom"] = date("c", strtotime($dateFrom));
        $query["DateTo"] = date("c", strtotime($dateTo));

        $orders = $this->authorization->query("{}", "getStatus?dateFrom=".$query["DateFrom"]."&dateTo=".$query["DateTo"], "GET");
        if(isset($orders['error'])) return ['error' => $orders['error']];

        foreach($orders->data as $orderId => $order) {

            if($order->statusCode != 'f510368a-8973-11e6-80c7-000d3a2542c4' && $order->statusCode != '9aaa55ed-6b97-11e6-80e9-003048baa05f' && $order->statusCode != '1094ba91-8ca3-11e6-80c7-000d3a2542c4') unset($orders->data[$orderId]);

        }

        $orders->data = array_values($orders->data);

        foreach($orders->data as $orderId => $order) {

            $response['SendingsInfo'][$orderId]['SenderInvoiceNumber'] = $order->clientId;
            $response['SendingsInfo'][$orderId]['PhoneNumber'] = $order->phone;
            $response['SendingsInfo'][$orderId]['DateOfCreate'] =  date("d.m.Y H:i:s", strtotime($order->dateOrder));
            $response['SendingsInfo'][$orderId]['InvoiceNumber'] = $order->id;

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

    }
    public function getInfo($orders='', $dateFrom='', $dateTo='') { //Получение информации по возвратным отправлениям

        return $response['error'] = 'Служба EasyWay не поддерживает получение информации о возвратах!';

    }

}

?>