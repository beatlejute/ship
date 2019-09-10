<?php

namespace cdek;

class returns extends \abstracts\returns {

    private $authorization;

    public function __construct($authorization)
    {
        $this->authorization = $authorization;
    }

    public function create($phone, $description, $recipientName='', $orderId='', $invoiceNumber='', $email='', $sum='') { //Регистрация возврата

        return $response['error'] = 'Служба CDEK не поддерживает регистрацию возвратов!';

    }
    public function getReturnsList($dateFrom, $dateTo) { //Получение списка возвратных отправлений

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        /*$query["DateFrom"] = date("Y-m-d", strtotime($dateFrom));
        $query["DateTo"] = date("Y-m-d", strtotime($dateTo." 23:59"));

        $xml = '<?xml version="1.0" encoding="UTF-8" ?>
                    <StatusReport Date="'.date("Y-m-d").'" Account="'.$this->authorization->authLogin.'" Secure="'.md5(date("Y-m-d").'&'.$this->authorization->authPassword).'">
                        <ChangePeriod DateFirst="'.$query["DateFrom"].'" DateLast="'.$query["DateTo"].'"  />
                    </StatusReport>';

        $request = array(
            'xml_request' => $xml
        );

        $requestinfo = $this->authorization->query($request, "status_report_h.php", "POST", "xml");

        foreach($requestinfo->getElementsByTagName('StatusReport')->item(0)->getElementsByTagName('Order') as $orderId => $order) {

            if($order->getElementsByTagName('Status')->item(0)->getAttribute('Code') != 5) unset($requestinfo->getElementsByTagName('StatusReport')->item(0)->getElementsByTagName('Order')->item($orderId));

        }

        $requestinfo->getElementsByTagName('StatusReport')->item(0)->getElementsByTagName('Order') = array_values($requestinfo->getElementsByTagName('StatusReport')->item(0)->getElementsByTagName('Order'));

        foreach($requestinfo->getElementsByTagName('StatusReport')->item(0)->getElementsByTagName('Order') as $orderId => $order) {

            $response['SendingsInfo'][$orderId]['SenderInvoiceNumber'] = $order->getAttribute('Number');
            $response['SendingsInfo'][$orderId]['InvoiceNumber'] = $order->getAttribute('DispatchNumber');
            $response['SendingsInfo'][$orderId]['ReturnReason'] = $order->getElementsByTagName('Reason')->item(0)->getAttribute('Description');

        }

        return $response;*/

        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        //return $response;

    }
    public function getInfo($orders='', $dateFrom='', $dateTo='') { //Получение информации по возвратным отправлениям

        return $response['error'] = 'Служба CDEK не поддерживает получение информации о возвратах!';

    }

}

?>