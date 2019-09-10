<?php

namespace abstracts;

abstract class authorization implements authorizationInterface {

    Protected $apiUrl;
    Protected $memcache;

    use authorizationTrait;

    abstract function login();
    abstract function logout();
    abstract function query($query, $url);

}

?>