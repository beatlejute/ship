<?php

namespace abstracts;


trait orderTrait
{

    public function getCostPreProcessing($get_defined_vars) {

        extract($get_defined_vars);

        if(!$fromCity) $answer['errors']['error']['info'][] = "Поле fromCity не заполнено.";
        if(!$fromRegion) $answer['errors']['error']['info'][] = "Поле fromRegion не заполнено.";
        if(!$toCity && !$toPoint) $answer['errors']['error']['info'][] = "Поля toCity или toPoint должны быть заполнены.";

        if(sizeof($answer['errors']['error']['info'])) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Не указаны обязательные параметры.";

            return $answer;

        }

        $from = $info->cityList($fromCity, $fromRegion);
        $fromId = $from[0]["Id"];

        if($toCity) {

            $to = $info->cityList($toCity, $toRegion);
            $toId = $to[0]["Id"];

        } elseif($toPoint) {

            $point = $info->parcelShopsList('', '', '', '', '', $toPoint);
            $to = $info->cityList($point[0]["CitiName"]);
            $toId = $to[0]["Id"];
            if($point[0]["tariffs"]) $pointTariffs = $point[0]["tariffs"];

        }

        $weight = $weight ? str_replace(",", ".", $weight) : 5;
        $length = $length ? str_replace(",", ".", $length) : 10;
        $width = $width ? floatval(str_replace(",", ".", $width)) : 10;
        $depth = $depth ? str_replace(",", ".", $depth) : 10;

        return get_defined_vars();

    }
    public function getCostPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        foreach($tariffs as $tariffId => $tariff) if($toPoint && ($tariffs[$tariffId]['mode'] == "Д-Д" || $tariffs[$tariffId]['mode'] == "С-Д")) unset($tariffs[$tariffId]);

        foreach($tariffs as $tariffId => $tariff) if(!$tariff['id']) {
            $errorInfo[] = 'Получен тариф без id: ' . print_r($tariff, true);
            unset($tariffs[$tariffId]);
        }
        foreach($tariffs as $tariffId => $tariff) if(!$tariff['cost']) {
            $errorInfo[] = 'Получен тариф без cost: ' . print_r($tariff, true);
            unset($tariffs[$tariffId]);
        }
        foreach($tariffs as $tariffId => $tariff) if(!$tariff['name']) {
            $errorInfo[] = 'Получен тариф без имени: ' . print_r($tariff, true);
            unset($tariffs[$tariffId]);
        }

        foreach($tariffs as $tariffId => $tariff) if(!in_array($tariff['mode'], ["Д-Д", "С-Д", "С-С", "Д-С"])) {
            $errorInfo[] = 'Получен не корректный тариф mode: ' . print_r($tariff, true);
            unset($tariffs[$tariffId]);
        }

        if($pointTariffs) foreach($tariffs as $tariffId => $tariff) if(!in_array($tariff['id'], $pointTariffs)) unset($tariffs[$tariffId]);

        if(!sizeof($tariffs)) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось получить данные ни одного подходящего тарифа.";
            if(isset($errorInfo)) $answer['errors']['error']['info'] = $errorInfo;

            return $answer;

        }

        $tariffs = array_values($tariffs);


        return get_defined_vars();

    }
    
    public function createPreProcessing($get_defined_vars) {
    
        extract($get_defined_vars);


        if(!$putdata) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Вы отправили пустое тело запроса.";

            return $answer;

        }

        $orders = $putdata;

    
        return get_defined_vars();
    
    }
    public function createPostProcessing($get_defined_vars) {
    
        extract($get_defined_vars);


        $response['CreatedSendings'] = array_values($response['CreatedSendings']);
        $response['RejectedSendings'] = array_values($response['RejectedSendings']);

        if(!sizeof($response['CreatedSendings'])) unset($response['CreatedSendings']);
        if(!sizeof($response['RejectedSendings'])) unset($response['RejectedSendings']);


        return get_defined_vars();
    
    }


    public function editPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$orderNumber && !$invoiceNumber) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Не указан номер отправления.";

            return $answer;

        }


        return get_defined_vars();

    }
    public function editPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$response["InvoiceNumber"]) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось найти отправление.";
            $answer['errors']['error']['info'] = $response;

            return $answer;

        }


        return get_defined_vars();

    }


    public function getInfoPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$orderNumber && !$invoiceNumber) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Не указан номер отправления.";

            return $answer;

        }


        return get_defined_vars();

    }
    public function getInfoPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$response['status']['State']) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Не удалось получить данные статуса отправления.";
            if(isset($errorInfo)) $answer['errors']['error']['info'] = $errorInfo;

            return $answer;

        }


        return get_defined_vars();

    }


    public function cancelPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if(!$orderNumber && !$invoiceNumber) {

            $answer['errors']['error']['code'] = "_3";
            $answer['errors']['error']['message'] = "Не указан номер отправления.";

            return $answer;

        }


        return get_defined_vars();

    }
    public function cancelPostProcessing($get_defined_vars) {

        extract($get_defined_vars);





        return get_defined_vars();

    }

}