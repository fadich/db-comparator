<?php


namespace royal\base\controllers;


use royal\base\Application;
use royal\base\Object;

abstract class BaseController extends Object
{
    protected $_view = 'index';

    public function render($view)
    {
        $this->_view = $view;
        return $this->includeView();
    }

    private function includeView()
    {
        return include_once ($this->findView());
    }

    private function findView()
    {
        return Application::basePath() . "/view/{$this->_view}.php";
    }

}
