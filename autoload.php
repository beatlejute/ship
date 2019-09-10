<?php

spl_autoload_register(function ($class_name) {

    $absolutePath = $_SERVER['DOCUMENT_ROOT'] ?: realpath(__DIR__);

    // convert namespace to full file path
    $class = $absolutePath . '/' . str_replace('\\', '/', $class_name) . '.php';
    if (file_exists($class)) require_once($class);

});