<?php

namespace Comparator;

/**
 * Class DbComparator
 *
 * @package     Comparator
 */
class DbComparator
{
    /** $database is mysqli object
     * @var  \mysqli
     */
    private $_database;
    private $_host;
    private $_dbName;
    private $_username;
    private $_password;
    private $_tables = [];
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
            $this->getTables();
        } catch (\Exception $e) {
            $this->errors[] = $e;
            throw new \Exception('<font color="#CC5555" size="4">' . $e->getMessage() . '</font>');
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

    private function getTables()
    {
        $result = $this->_database->query('SHOW TABLES')->fetch_all();
        foreach ($result as $item) {
            $res = $this->_database->query("SHOW COLUMNS FROM " . $item[0])->fetch_array();
            $this->_tables[$item[0]]['Field']   = $res['Field'];
            $this->_tables[$item[0]]['Type']    = $res['Type'];
            $this->_tables[$item[0]]['Null']    = $res['Null'];
            $this->_tables[$item[0]]['Key']     = $res['Key'];
            $this->_tables[$item[0]]['Default'] = $res['Default'];
            $this->_tables[$item[0]]['Extra']   = $res['Extra'];
        }
    }

    public function showTables()
    {
        echo '<h3>Tables of ' . $this->_dbName . ':</h3>';
        foreach ($this->_tables as $key => $table) {
            echo '<ul><strong>' . $key . '</strong><br>';
            foreach ($table as $col => $val) {
                echo '<li><font size="2" >' . $col . ' - ' . $val . '</font>';
            }
            echo '</ul>';
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