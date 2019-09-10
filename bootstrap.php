<?php

    /**
     * Формирование заголовков
     **/
    header('Content-type: application/json');

    $path = $_SERVER['REQUEST_URI'];
    list($prefix, $namespace, $class, $method) = explode('/', $path);
    $authfree = ($class == 'info' || $method == 'getCost');


    /**
     * Подключаем конфиги
     */
    require_once('config.php');


    /**
     * Авторизация
     **/
    $authorizationClass = '\\'.$namespace.'\\authorization';
    if (class_exists($authorizationClass)) {

        $authorization = new $authorizationClass($auth[$namespace], $memcache);

    } else {

        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

        $answer['error']['code'] = "_0";
        $answer['error']['message'] = "Вызываемый метод не существует.";
        $answer['error']['info'][] = $namespace;

        die(json_encode($answer, JSON_UNESCAPED_UNICODE));

    }
    if($error = $authorization->login()) die($error);

    /**
     * Создаем объект класса
     **/
    $className = '\\'.$namespace.'\\'.$class;
    if (class_exists($className)) {

        $classObject = new $className($authorization, $memcache);

    } else {

        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

        $answer['error']['code'] = "_0";
        $answer['error']['message'] = "Вызываемый метод не существует.";
        $answer['error']['info'][] = $className;

        die(json_encode($answer, JSON_UNESCAPED_UNICODE));

    }


    /**
     * Формеруем аргументы для метода
     **/
    if(method_exists($classObject, $method)) {

        $reflection = new ReflectionMethod($classObject, $method);
        $fire_args = [];
        foreach($reflection->getParameters() AS $arg)
        {
            if($_REQUEST[$arg->name])
                $fire_args[$arg->name]=$_REQUEST[$arg->name];
            elseif($arg->name == 'putdata') {

                $putdata = file_get_contents('php://input');

                if(substr($putdata, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) $putdata = substr($putdata, 3);

                $putdecodedata = json_decode($putdata, true);

                if($putdata && !$putdecodedata) {

                    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

                    $answer['error']['code'] = "_3";
                    $answer['error']['message'] = "В теле запроса не корректный JSON.";
                    $answer['error']['info'][] = $putdata;

                    die(json_encode($answer, JSON_UNESCAPED_UNICODE));

                }

                $fire_args[$arg->name] = $putdecodedata;

            } else
                $fire_args[$arg->name]=null;
        }

    } else {

        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

        $answer['error']['code'] = "_0";
        $answer['error']['message'] = "Вызываемый метод не существует.";
        $answer['error']['info'][] = $method;

        die(json_encode($answer, JSON_UNESCAPED_UNICODE));

    }



    /**
     * Вызываем метод
     */
    if(method_exists($classObject, $method)) {
        $responce = call_user_func_array(array($classObject, $method), $fire_args);
    } else {

        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

        $answer['error']['code'] = "_0";
        $answer['error']['message'] = "Вызываемый метод не существует.";
        $answer['error']['info'][] = $method;

        die(json_encode($answer, JSON_UNESCAPED_UNICODE));

    }

    /**
     * Формируем вывод ошибок
     */
    if(array_key_exists('error', $responce)) header($_SERVER['SERVER_PROTOCOL'] . ' 418 I’m a teapot', true, 418);

    /**
     * Формируем и выводим JSON
     */
    print json_encode($responce, JSON_UNESCAPED_UNICODE);