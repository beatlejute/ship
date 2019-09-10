<?php

namespace pickpoint;

class authorization extends \abstracts\authorization {

    public $sessionId;
    public $ikn;
    private $login;
    private $password;


    public function __construct($auth, $memcache=false) {
        $this->login = $auth['login'];
        $this->password = $auth['password'];
        $this->ikn = $auth['ikn'];
        $this->apiUrl = $auth['test'] ? 'https://e-solution.pickpoint.ru/apitest/' : 'https://e-solution.pickpoint.ru/api/';
        $this->memcache = $memcache;
    }

    public function login() { // Начало сессии

        $query["Login"] = $this->login;
        $query["Password"] = $this->password;

        /**** Memcache ****/
        $cacheKey = "pickpointsessionId";

        if($this->memcache) $this->sessionId = $this->memcache->get($cacheKey);

        if(empty($this->sessionId)) {
            /**** Memcache ****/

            $answer = $this->query(json_encode($query), "login");
            if($answer['ErrorMessage']) return $answer['ErrorMessage'];
            else $this->sessionId = $answer['SessionId'];

            /**** Memcache ****/

            if($this->memcache) $this->memcache->set($cacheKey, $this->sessionId, 5);

        }
        /**** Memcache ****/

        /*
        Начало сессии (Login)
        URL: /login
        Метод: POST
        Описание
        Команда предназначена для начала сеанса работы. В запросе отправляемся логин и пароль, в случае правильности, возвращается уникальный номер сессии, который действителен 12 часов, если по ней небыло Logout. Вся дальнейшая работа ведется на основании номера сессии (одну сессию можно использовать для любого запроса, пока она валидна).
        Структура запроса
        {
            "Login":"<логин (50 символов)>",>",
            "Password":"<пароль (20 символов)>"
        }

        Структура ответа
        {
            "SessionId":"<уникальный идентификатор сессии  (GUID 16 байт)>",
            "ErrorMessage":"<текстовое сообщение об ошибке (200 символов)>"
        }
        */

    }
    public function logout() { // Завершение сессии

        $query["SessionId"] = $this->sessionId;

        $answer = $this->query(json_encode($query), "logout");
        if($answer['Success'] == false) return 'Завершить сессию не удалось!';
        elseif($this->memcache) $this->memcache->delete("pickpointsessionId");

        /*
        Завершение сессии (Logout)
        URL: /logout
        Метод: POST
        Описание
        Команда предназначена для завершения сеанса работы. В запросе отправляется идентификатор сессии. В ответ возвращается признак успешности выполнения.
        Структура запроса
        {
            "SessionId":	"<уникальный идентификатор сессии  (GUID 16 байт)>"
        }

        Структура ответа
        {
            "Success":	<true/false>
        }
        */

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
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HTTPHEADER => array(
                //"Authorization:Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MzY3MiwidmVycyI6MCwiY2xpZW50SWQiOjM0ODcsImluc3RhbmNlSWQiOiJhcGlwcm9kIiwiaWF0IjoxNDg3MjM0NDEwfQ.kEMLUji5_MBaR75rRpKfz7rZeIxd1QJEQAKnmf760Sg",
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
