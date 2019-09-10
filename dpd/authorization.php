<?php

namespace dpd;

class authorization extends \abstracts\authorization {

    public $authLogin;
    public $authPassword;

    public function __construct($auth, $memcache=false) {
        $this->authLogin = $auth['login'];
        $this->authPassword = $auth['password'];
        $this->apiUrl = $auth['apiurl'];
        $this->memcache = $memcache;
    }
    public function login() { // Начало сессии
    }
    public function logout() { // Завершение сессии
    }
    public function query($query, $url, $method='', $requestContainer=false) {

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $wsdl = $this->apiUrl . $url;
        $soap = true;
        try {

            $client = new \SoapClient($wsdl);

        } catch (\Exception $e) {

            $answer['error']['code'] = "_1";
            $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";
            $answer['error']['info'][] = "Soap Client Error #: " . $e->getMessage();
            $answer['error']['info'][]["Адрес запроса к ТК"] = $this->apiUrl.$url;
            $answer['error']['info'][]["Тело запроса к ТК"] = $query;

            return $answer;

        }

        //данные авторизации
        $query['auth']['clientNumber'] = $this->authLogin;
        $query['auth']['clientKey'] = $this->authPassword;

        if($requestContainer) $queryrequest[$requestContainer] = $query; else $queryrequest = $query;
        try {

            $response = $client->$method($queryrequest);

        } catch (\Exception $e) { 

            $answer['error']['code'] = "_1";
            $answer['error']['message'] = "Не удалось получить данные от сервиса ТК.";
            $answer['error']['info'][] = "Soap Client Error #: " . $e->getMessage();
            $answer['error']['info'][]["Адрес запроса к ТК"] = $this->apiUrl.$url;
            $answer['error']['info'][]["Тело запроса к ТК"] = $query;

            return $answer;

        }
        $response = $response->return;


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

    }
}