<?php

namespace easyway;

class batches extends \abstracts\batches {

    public function getOrderList($dateFrom, $dateTo) { //Отбор списка заказов за временной интервал

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $query["DateFrom"] = date("c", strtotime($dateFrom));
        $query["DateTo"] = date("c", strtotime($dateTo." 23:59"));

        $orders = $this->authorization->query("{}", "getStatus?dateFrom=".$query["DateFrom"]."&dateTo=".$query["DateTo"], "GET");
        if(isset($orders['error'])) return ['error' => $orders['error']];

        if(!$orders) {

            $answer['error']['code'] = "_1";
            $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";

            return $answer;

        }
        if(!is_array($orders)) {

            $answer['error']['code'] = "_2";
            $answer['error']['message'] = "Сервис ТК вернул некорректный ответ.";
            $answer['error']['info'][] = print_r($orders, true);

            return $answer;

        }

        foreach($orders['data'] as $orderId => $order) {

            $response['Invoices'][$orderId]['CustomerNumber'] = $order['clientId'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Code'] = $order['statusCode'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Description'] = $order['status'];
            $response['Invoices'][$orderId]['Encloses']['Statuses'][0]['Modified'] =  date("d.m.Y H:i:s", strtotime($order['date']));
            $response['Invoices'][$orderId]['Number'] = $order['id'];

        }


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $response;

        /*
        getStatus

        Запрос статусов заявок за интервал времени

        GET-запрос http://apiurl/getStatus?dateFrom=2017-09-10T00:00:00&dateTo=2017-10-10T00:00:00

        dateFrom - начальная дата отбора
        dateTo - конечная дата отбора
        Пример ответа:

        [
        {
        "orderNumber": "1091840-YD1854000",
        "date": "2017-05-20T11:58:42",
        "status": "На ПВЗ",
        "arrivalPlanDateTime": "2017-05-22T15:00:00",
        "dateOrder": "2017-05-15T15:08:00",
        "sender": "Московская обл.",
        "receiver": "Самара",
        "carrierTrackNumber": "000029766",
        "address": "Терминал, Склад ПЭК, г. Ярославль, проспект Октября, 93",
        "deliveryType": "Терминал",
        "phone": "",
        "id": "000038700",
        "statusCode": "675f4358-6f61-11e6-80ea-003048baa05f"
        },
        {
        "orderNumber": "66012-YD1854327",
        "date": "2017-05-22T13:32:37",
        "status": "Выдан",
        "arrivalPlanDateTime": "2017-05-20T15:00:00",
        "dateOrder": "2017-05-15T15:27:17",
        "sender": "Москва",
        "receiver": "Брянск",
        "carrierTrackNumber": "000029779",
        "address": "Терминал, Склад ПЭК, г. Ярославль, проспект Октября, 93",
        "deliveryType": "Терминал",
        "phone": "",
        "id": "000038700",
        "statusCode": "b3e0596a-6b97-11e6-80e9-003048baa05f"
        }
        ]
        */
    }
    public function getLabel($putdata='', $dateFrom='', $dateTo='') { //Печать Наклеек

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $response = $this->authorization->query("{}", "getLabel?number=".join($orders, ','), "GET", false);
        if(isset($response['error'])) return ['error' => $response['error']];


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        require_once 'utils/mpdf/vendor/autoload.php';

        $mpdf = new \mPDF();
        $mpdf->WriteHTML($response);
        $mpdf->Output();

        die();

        //return $response;

        /*
        getLabel

        Получение этикеток для печати

        GET-запрос http://apiurl/getLabel?number=000026718,000033668

        number - номера заявок, разделенные запятой
        */

    }
    public function create($orders='', $dateFrom='', $dateTo='') { //Создаёт партию

        return $response['error'] = 'Служба EasyWay не поддерживает партии!';

    }
    public function getInfo($invoiceNumber='', $reestrNumber='') { //Запрашивает данные об партиях

        return $response['error'] = 'Служба EasyWay не поддерживает партии!';

    }
    public function removeOrder($orderNumber='', $invoiceNumber='') { //Исключение заказа на доставку из всех партий

        return $response['error'] = 'Служба EasyWay не поддерживает партии!';

    }

}
