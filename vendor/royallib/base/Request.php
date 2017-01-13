<?php

namespace royal\base;

/**
 * Class Request
 * @package royal\base
 *
 * @property string $version
 * @property string $isAjax
 */
class Request extends Object
{
    protected $server;

    public function __construct() {  }

    public function redirect($url, $code = 302)
    {
        header("Location: {$url} ", true, $code); // HTTP/{$this->version}
        exit();
    }

    protected function getVersion()
    {
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
            return '1.0';
        } else {
            return '1.1';
        }
    }

    protected function getIsAjax()
    {
        return isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest";
    }
}
