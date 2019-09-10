<?php

namespace easyway;

class authorization extends \abstracts\authorization {

    private $authLogin;
    private $authPassword;

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
    public function query($query, $url, $method='POST', $json=true) { // запрос к API

        $PreProcessing = __FUNCTION__.'PreProcessing';
        if(method_exists($this, $PreProcessing)) extract($this->$PreProcessing(get_defined_vars()));
        if($errors) return $errors;


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl.$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_USERPWD => $this->authLogin.":".$this->authPassword,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
            ),
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
