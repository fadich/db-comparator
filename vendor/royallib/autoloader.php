<?php

spl_autoload_register(function ($class_name) {
    $appName = defined('APP_NAME') ? APP_NAME : define('APP_NAME', 'app');
    if ($appName == 'royal') {
        throw new Error("Please, set an app name other than 'royal'.");
    }
    $root = explode('\\', $class_name)[0];
    if ($root == 'royal') {
        include_once __DIR__ . substr($class_name, 5) . ".php";
    } elseif ($root == $appName) {
        include_once __DIR__ . "/../../" . substr($class_name, strlen($appName) + 1) . ".php";
    } else {
        include_once "{$class_name}.php";
    }
});
