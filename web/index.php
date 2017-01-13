<?php

defined('APP_NAME') or define('APP_NAME', 'comparator');

require(__DIR__ . "/../vendor/royallib/autoloader.php");
require(__DIR__ . "/../vendor/royallib/error_handler.php");

\royal\base\Application::run();
