<?php

	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

	if($authfree) header("Access-Control-Allow-Origin: *");

	//Авторизация
	list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =  explode(':', base64_decode(substr($_SERVER['REDIRECT_HTTP_AUTHORIZATION'], 6)));
	if(isset($_SERVER['HTTP_AUTHORIZATION'])) list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) =  explode(":" , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
	$no_auth = (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!== 'test' || $_SERVER['PHP_AUTH_PW']!== 'test');

	if($no_auth == TRUE && !$authfree) {

		header('WWW-Authenticate: Basic realm="protected"');
		header('HTTP/1.0 401 Unauthorized');

		die('Доступ запрещён!');

	}

    if (extension_loaded('memcached')) {

        $memcache = new Memcached;
        $memcache->addServer('/var/run/memcached/memcached1.sock', 0);

    } else {

        $memcachedClass = 'memcache';
        $memcache = new $memcachedClass;
        $memcache->connect('localhost', 11211);

    }

    require_once('autoload.php');

	//Автаризационные тестовые данные pickpoint
	$auth['pickpoint']['login'] = 'apitest';
	$auth['pickpoint']['password'] = 'apitest';
	$auth['pickpoint']['ikn'] = '9990003041';
	$auth['pickpoint']['test'] = true;


	//Параметры тестовой учетной записи СДЭК
	$auth['cdek']['Account'] = 'c15d33781d0693d2d2f81a8447cfb376';
	$auth['cdek']['Secure_password'] = '6ad48209f6f7f17050766af395e5de5e';
	$auth['cdek']['Email'] = '';

	//Автаризационные данные EasyWay
	$auth['easyway']['apiurl'] = 'https://lk.easyway.ru/EasyWay/hs/EWA_API/v2/';
	$auth['easyway']['login'] = '***';
	$auth['easyway']['password'] = '******';

	//Тестовые автаризационные данные DPD
	$auth['dpd']['apiurl'] = 'http://wstest.dpd.ru/services/';
	$auth['dpd']['login'] = '1001040551';
	$auth['dpd']['password'] = '1F4B2F546FF305E84E510384C8D438B03232276B';

	//Автаризационные данные shiptor
	$auth['shiptor']['apiurl'] = 'https://api.shiptor.ru/';
	$auth['shiptor']['privateKey'] = '****';
	$auth['shiptor']['publicKey'] = '*****';

?>