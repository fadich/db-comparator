<?php


namespace comparator\controllers;


use royal\type\Mixed;
use comparator\core\DbComparator;
use royal\base\controllers\Controller;

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
        $to   = (new Mixed($_POST['to']))->explodeElements('%%', '=>', [], Mixed::FORMAT_OBJECT)->value;
        try {
            $from = new DbComparator($from->host, $from->username, $from->database, $from->password);
            $to   = new DbComparator($to->host,   $to->username,   $to->database,   $to->password);
        } catch (\Exception $e) {
            $this->redirect('/main/join', 400);
        }
        $this->redirect('/' . (isset($_POST['params']) ? $_POST['params'] : ''), $from->join($to) ? 200 : 400);
    }
}
