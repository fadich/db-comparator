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
        echo '<pre>'; var_dump($_GET); die;
        $db = new DbComparator();
        echo '<pre>'; var_dump($db->getContent()); die;
    }
}
