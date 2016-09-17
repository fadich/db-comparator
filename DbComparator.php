<?php

namespace Comparator;

/**
 * Class DbComparator
 *
 * @package     Comparator
 */
class DbComparator
{
    private $_host;
    private $_dbName;
    private $_username;
    private $_password;

    /** @var  \mysqli $database is mysqli object*/
    private $_database;
    private $errors = [];

    /**
     * DbComparator constructor.
     *  
     * @param bool $host
     * @param bool $username
     * @param bool $dbName
     * @param null $password
     * @throws \Exception
     */
    public function __construct($host = false, $username = false, $dbName = false, $password = null)
    {
        if (!($host && $username && $dbName)) {
            throw new \Exception('Incorrect connection params.');
        }
        $this->_host     = $host;
        $this->_username = $username;
        $this->_dbName   = $dbName;
        $this->_password = $password;
        try {
            $this->connection();
        } catch (\Exception $e) {
            $this->errors[] = $e;
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Connect to database
     * @throws \Exception
     */
    private function connection()
    {
        mysqli_report(MYSQLI_REPORT_STRICT);
        $this->_database = new \mysqli($this->_host, $this->_username, $this->_password, $this->_dbName);
        if ($this->_database->connect_error) {
            throw new \Exception('Can not connect to ' . $this->_username . '@' . $this->_host);
        }
    }

    public function showTables()
    {
        $result = $this->_database->query('SHOW TABLES')->fetch_all();
        echo '<h3>Tables of ' . $this->_dbName . ':</h3>';
        foreach ($result as $res) {
            foreach ($res as $re) {
                echo $re . '<br>';
            }
        }
    }

    /** @return bool */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    public function compare(DbComparator $db)
    {
        
    }
}