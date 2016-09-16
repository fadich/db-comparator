<?php
include_once ('DbComparator.php');

use Comparator\DbComparator;

if (empty($_GET))
{
    echo "hello comparator";
}

$params = [];

try{
    $comparator = new DbComparator($params);
} catch (\Exception $e) {
    echo '<br>' . $e->getMessage();
}