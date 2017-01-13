<?php


namespace royal\base\controllers;


use royal\base\Object;
use royal\base\Application;

abstract class BaseController extends Object
{
    protected $_view = 'index';

    public function render($view)
    {
        $this->_view = $view;
        return $this->includeView();
    }

    public function redirect($url, $code = 302)
    {
        Application::$request->redirect($url, $code);
    }

    private function includeView()
    {
        return include_once($this->findView());
    }

    private function findView()
    {
        return Application::basePath() . "/view/{$this->_view}.php";
    }
}
