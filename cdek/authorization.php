<?php

namespace cdek;

class authorization extends \abstracts\authorization {

    public $authLogin;
    public $authPassword;

    public function __construct($auth, $memcache=false) {
        $this->authLogin = $auth['Account'];
        $this->authPassword = $auth['Secure_password'];
        $this->apiUrl = 'https://integration.cdek.ru/';
        $this->calculatorApiUrl = 'https://api.cdek.ru/';
        $this->memcache = $memcache;
    }

    public function login() { // Начало сессии
    }
    public function logout() { // Завершение сессии
    }
    public function query($query, $url, $method='POST', $format='', $api='integration') { // запрос к API

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        switch ($api) {

            case 'api':
                $apiUrl = $this->calculatorApiUrl;
                break;
            default:
                $apiUrl = $this->apiUrl;

        }

        switch ($format) {

            case 'json':
                $json = true;
                break;
            case 'xml':
                $xml = true;
                break;

        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $PostProcessing = __FUNCTION__.'PostProcessing';
        if(method_exists($this, $PostProcessing)) extract($this->$PostProcessing(get_defined_vars()));
        if($errors) return $errors;

        return $answer;

    }

}
