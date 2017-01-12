<?php


namespace royal\base;



use royal\type\Str;

final class Application extends Object
{
    private $_url;
    private $_controller;
    private $_scenario;

    private function __construct() {  }

    public static function run()
    {
        $con = new static();
        $con->_url = explode("?", $_SERVER['REQUEST_URI'])[0];
        $con->_controller = explode("/", $con->_url)[0];
        $con->_scenario   = explode("/", $con->_url)[1] ?? '';
        try {
            $con->call();
        } catch (\Throwable $throwable) {
            echo '<pre>'; die($con->displayError($throwable));
        }
    }

    private function call()
    {
        if ($this->_controller) {
            $controller = new $this->controllerClass;
            if ($this->_scenario) {
                $scenario = "s{$this->_scenario}";
                return $controller->$scenario();
            }
            return $controller->sIndex();
        }
        $this->_controller = 'main';
        return (new $this->controllerClass)->sIndex();
    }

    protected function getControllerClass()
    {
        return APP_NAME . '\\controllers\\' . (new Str($this->_controller))->toClassName() . "Controller";
    }

    /**
     * @param null|\Throwable $throwable
     *
     * @return string
     */
    private function displayError($throwable)
    {
        if ($throwable instanceof \Throwable) {
            return "<b>" . get_class($throwable) . "</b>: " . $throwable->getMessage() . ". "
                . $throwable->getFile() . ":" . $throwable->getLine() . "\n" . $this->displayError($throwable->getPrevious());
        }
        return '';
    }
}
