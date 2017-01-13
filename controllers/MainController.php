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

    public function aJoin()
    {
        if (!isset($_POST['from']) || !isset($_POST['to']) || !$this->request->isAjax) {
            $this->redirect('/' . (isset($_POST['params']) ? '?' . $_POST['params'] : ''));
        }
        $from = (new Mixed($_POST['from']))->explodeElements('%%', '=>', [], Mixed::FORMAT_OBJECT)->value;
        $to   = (new Mixed($_POST['to']))->explodeElements('%%', '=>')->value;
        try {
            $from = new DbComparator(
                $from->host ?? null,
                $from->username ?? null,
                $from->database ?? null,
                $from->password ?? null
            );
            $to = new DbComparator(
                $to->host ?? null,
                $to->username ?? null,
                $to->database ?? null,
                $to->password ?? null
            );
        } catch (\Exception $e) {
            $this->redirect('main/join', 400);
        }
        echo '<pre>'; var_dump($from->join($to)); die;
    }
}
