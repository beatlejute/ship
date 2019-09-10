<?php

namespace dpd;

class returns extends \abstracts\returns {

    public function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum='') { //Регистрация возврата

        return $response['error'] = 'Служба dpd не поддерживает регистрацию возвратов!';

    }
    public function getReturnsList($dateFrom, $dateTo) { //Получение списка возвратных отправлений

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query['params']['archived'] = false;
        $query['params']['delivered'] = true;
        $query['params']['status'] = 'returned';


        $request = $this->authorization->query(json_encode($query), "shipping/v1", "POST", true, false);
        if($request['error']) return ['error' => $request['error']];


        foreach($request as $orderId => $order) if(strtotime($order['created_at']) >= strtotime($dateFrom) && strtotime($order['created_at']) <= strtotime($dateTo." 23:59")) {

            $response['SendingsInfo'][$orderId]['SenderInvoiceNumber'] = $order['external_id'];
            $response['SendingsInfo'][$orderId]['InvoiceNumber'] = $order['id'];
            //$response['SendingsInfo'][$orderId]['ReturnReason'] = $order->getElementsByTagName('Reason')->item(0)->getAttribute('Description');

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*

        */

    }
    public function getInfo($orders='', $dateFrom='', $dateTo='') { //Получение информации по возвратным отправлениям

        return $response['error'] = 'Служба dpd не поддерживает получение информации о возвратах!';

    }

}

?>