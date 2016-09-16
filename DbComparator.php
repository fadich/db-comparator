<?php

namespace Comparator;

class DbComparator
{
    private $host;
    private $dbName;
    private $username;
    private $password;

    public function __construct($host = false, $dbName = false, $username = false, $password = null)
    {
        if (!($host && $dbName && $username)) {
            throw new \Exception('Incorrect connection params.');
        }
    }
    
    public static function compare(DbComparator $db)
    {
        
    }
}