<?php
include_once ('DbComparator.php');

use Comparator\DbComparator;
/** Hello (head) */
echo '<h1 align="center">Database comparator</h1>';
echo '<hr>';
if (empty($_GET)) {
    echo '<li>Please, enter params for database connection via GET request.<br>';
    echo '<li>Set value of any GET-variable as a string having the structure "[host]%%[username]%%[database]%%[password]".<br>';
} else {
    foreach ($_GET as $item)
    {
        $connection = explode('%%', $item);
        $databasesCon[] = $connection;
    }
}

if (!empty($databasesCon)):
    foreach ($databasesCon as $databaseCon) {
        try {
            $db = new DbComparator($databaseCon[0], $databaseCon[1], $databaseCon[2], $databaseCon[3]);
            if (!$db->hasErrors()) {
                $db->showTables();
            } else { }
        } catch (\Exception $e) {
            echo '<br>' . $e->getMessage();
        }
    }
endif;
