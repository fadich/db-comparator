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
            $bases[] = $db = new DbComparator($databaseCon[0], $databaseCon[1], $databaseCon[2], $databaseCon[3]);
            if (!$db->hasErrors()) {
                $db->showTables();
            } else { }
        } catch (\Exception $e) {
            echo '<br>' . $e->getMessage();
        }
    }
    $length = sizeof($bases);
    if ($length > 1) {
        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < $length; $j++) {
                if ($i !== $j) {
                    echo '<hr><h2 align="center">Comparing ' . $bases[$i]->getDbName()
                        . ' and ' . $bases[$j]->getDbName() . '</h2>';
                    $compareResult = $bases[$i]->compare($bases[$j]);
                    echo $compareResult ?
                        '<font size="4"><strong>Database ' . $bases[$i]->getDbName() . ' has:</strong></font><br>' . $compareResult :
                        '<h3><font size="4" color="green">Identical databases.</font></h3>';
                }
            }
        }
    }
endif;
