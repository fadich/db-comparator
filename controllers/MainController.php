<?php


namespace comparator\controllers;


use royal\base\controllers\Controller;

class MainController extends Controller
{
    public function aIndex()
    {
        return $this->render('index');
    }
}
