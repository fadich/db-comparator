<?php

namespace royal\base;

/**
 * Class Request
 * @package royal\base
 *
 * @property string $version
 */
class Request extends Object
{

    public function redirect($url, $code = 302)
    {
        header("Location: {$url} ", true, $code); // HTTP/{$this->version}
    }

    protected function getVersion()
    {
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
            return '1.0';
        } else {
            return '1.1';
        }
    }

}
