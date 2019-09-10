<?php

namespace abstracts;

interface authorizationInterface { //Авторизация

    function login();
    function logout();
    function query($query, $url); // запрос к API

}