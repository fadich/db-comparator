<?php


namespace comparator\controllers;


use comparator\core\DbComparator;
use royal\base\controllers\Controller;
use royal\type\Mixed;

class MainController extends Controller
{
    public function aIndex()
    {
        return $this->render('index');
    }

    public function aMerge()
    {
        $from = (new Mixed($_POST['from'] ?? ''))->explodeElements('%%', '=>', [], Mixed::FORMAT_OBJECT)->value;
        $to   = (new Mixed($_POST['to']   ?? ''))->explodeElements('%%', '=>')->value;
        try {
            $from = new DbComparator(
                $from->host ?? null,
                $from->username ?? null,
                $from->database ?? null,
                $from->password ?? null
            );
            $to = new DbComparator(
                789, //$to->host ?? null,
                $to->username ?? null,
                $to->database ?? null,
                $to->password ?? null
            );
        } catch (\Exception $e) {
            $this->redirect('/' . (isset($_POST['params']) ? '?' . $_POST['params'] : ''));
        }
        echo '<pre>'; var_dump($from->getContent()); die;
    }
}
