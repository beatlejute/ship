<?php

namespace abstracts;


trait authorizationTrait {

    public function queryPreProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if($_GET['test']) print $query.',';


        return get_defined_vars();

    }
    public function queryPostProcessing($get_defined_vars) {

        extract($get_defined_vars);


        if($_GET['test']) print "{'".$this->apiUrl.$url."':    ".$response."},";

        /*$log = fopen('../logs/'.time().'.txt', 'w');
        fwrite($log, $query.','."{'".$this->apiUrl.$url."':    ".$response."}");
        fclose($log);*/


        if (!$response) {

            $answer['errors']['error']['code'] = "_1";
            $answer['errors']['error']['message'] = "Не удалось получить данные от сервиса ТК.";
            $answer['errors']['error']['info'][] = "cURL Error #: Пустой ответ от сервиса";
            $answer['errors']['error']['info'][]["Адрес запроса к ТК"] = $this->apiUrl.$url;
            $answer['errors']['error']['info'][]["Тело запроса к ТК"] = $query;

            return $answer;

        } elseif ($err) {

            $answer['errors']['error']['code'] = "_2";
            $answer['errors']['error']['message'] = "Сервис ТК вернул некорректный ответ.";
            $answer['errors']['error']['info'][] = "cURL Error #:" . $err;
            $answer['errors']['error']['info'][]["Адрес запроса к ТК"] = $this->apiUrl.$url;
            $answer['errors']['error']['info'][]["Тело запроса к ТК"] = $query;
            $answer['errors']['error']['info'][]["Ответ ТК:"] = $response;

            return $answer;

        } else {

            if($json || $xml || $soap) {

                if($xml) {

                    $xml = simplexml_load_string($response);
                    $response = json_encode($xml, JSON_UNESCAPED_UNICODE);

                }
                if($soap) {

                    $response = json_encode($response, JSON_UNESCAPED_UNICODE);

                }

                if($ret = json_decode($response, true)) {

                    if(!is_array($ret)) {

                        $answer['errors']['error']['code'] = "_2";
                        $answer['errors']['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                        $answer['errors']['error']['info'][] = print_r($ret, true);
                        $answer['errors']['error']['info'][]["Адрес запроса к ТК"] = $this->apiUrl.$url;
                        $answer['errors']['error']['info'][]["Тело запроса к ТК"] = $query;
                        $answer['errors']['error']['info'][]["Ответ ТК:"] = $response;

                        return $answer;

                    }

                    $answer = $ret;

                } else {
                    $answer['errors']['error']['code'] = "_2";
                    $answer['errors']['error']['message'] = "Сервис ТК вернул некорректный ответ.";
                    $answer['errors']['error']['info'][]["Адрес запроса к ТК"] = $this->apiUrl.$url;
                    $answer['errors']['error']['info'][]["Тело запроса к ТК"] = $query;
                    $answer['errors']['error']['info'][]["Ответ ТК:"] = $response;

                    return $answer;
                }

            } else {

                $answer = $response;

            }

        }


        return get_defined_vars();

    }

}