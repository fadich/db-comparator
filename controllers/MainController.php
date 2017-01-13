<?php


namespace comparator\controllers;


use comparator\core\DbComparator;
use royal\base\controllers\Controller;

class MainController extends Controller
{
    public function aIndex()
    {
        return $this->render('index');
    }

    public function aMerge()
    {
        $db = new DbComparator();
        echo '<pre>'; var_dump($db->getContent()['db']); die;
    }
}
