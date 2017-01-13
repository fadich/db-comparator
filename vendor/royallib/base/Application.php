<?php


namespace royal\base;


use royal\base\exceptions\BadRequestException;
use royal\type\Str;

final class Application extends Object
{
    /** @var Request $request */
    public static $request;

    private static $_baseAppPath;

    private $_url;
    private $_controller;
    private $_action;

    private function __construct() {  }

    public static function run()
    {
        self::$request = new Request();
        self::$_baseAppPath = __DIR__ . '/../../../';
        $con = new static();
        $con->_url = explode("?", $_SERVER['REQUEST_URI'])[0];
        $con->_controller = explode("/", $con->_url)[1] ?? '';
        $con->_action   = explode("/", $con->_url)[2] ?? '';
        try {
            $con->call();
        } catch (BadRequestException $exception) {
            Application::$request->redirect('', 404);
        } catch (\Throwable $throwable) {
            echo '<pre>'; die($con->displayError($throwable));
        }
    }

    public static function basePath()
    {
        return self::$_baseAppPath;
    }

    private function call()
    {
        if ($this->_controller) {
            $controller = new $this->controllerClass;
            if ($this->_action) {
                $scenario = "a{$this->_action}";
                try {
                    return $controller->$scenario();
                } catch (\Error $error) {
                    throw new BadRequestException("Unknown scenario {$this->_controller}/{$this->_action}");
                }
            }
            return $controller->aIndex();
        }
        $this->_controller = 'main';
        return (new $this->controllerClass)->aIndex();
    }

    protected function getControllerClass()
    {
        return '\\' . APP_NAME . '\\controllers\\' . (new Str($this->_controller))->toClassName() . "Controller";
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
