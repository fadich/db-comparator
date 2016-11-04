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
    private $_errors    = [];
    private $_structure = [];

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
        $this->_host     = $host;
        $this->_username = $username;
        $this->_dbName   = $dbName;
        $this->_password = $password;
        $this->validate();
        try {
            $this->connection();
            $this->getTables();
        } catch (\Exception $e) {
            $this->_errors[] = $e;
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Connect to database
     * @throws \Exception
     */
    private function connection()
    {
        if ($this->hasErrors()) {
            throw new \Exception('Error connection to ' . $this->_username . '@' . $this->_host);
        }
        mysqli_report(MYSQLI_REPORT_STRICT);
        $this->_database = new \mysqli($this->_host, $this->_username, $this->_password, $this->_dbName);
        if ($this->_database->connect_error) {
            $this->_errors[]   = 'Error connection to ' . $this->_username . '@' . $this->_host;
            throw new \Exception('Error connection to ' . $this->_username . '@' . $this->_host);
        }
    }

    private function getTables()
    {
        $result = $this->_database->query('SHOW TABLES')->fetch_all();
        foreach ($result as $item) {
            $res = $this->_database->query("SHOW COLUMNS FROM " . $item[0])->fetch_all();
            foreach ($res as $re) {
                $this->_structure[$item[0]][$re[0]]['Type']     = $re[1];
                $this->_structure[$item[0]][$re[0]]['Null']     = $re[2];
                $this->_structure[$item[0]][$re[0]]['Key']      = $re[3];
                $this->_structure[$item[0]][$re[0]]['Default']  = $re[4];
                $this->_structure[$item[0]][$re[0]]['Extra']    = $re[5];
            }
        }
    }

    public function getContent()
    {
        return !empty($this->_structure) ? $this->_structure : false;
    }
    
    /** @return bool */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function getDbName()
    {
        return $this->_host . "@" . $this->_dbName;
    }
    
    public function compare(DbComparator $db)
    {
        foreach ($this->_structure as $key => $table){
            if (!isset($db->_structure[$key])) {
                $result[$key] = $key;
            } else {
                foreach ($table as $ke => $column) {
                    if (!isset($db->_structure[$key][$ke])){
                        $result[$key][$ke] = $ke;
                    } else {
                        foreach ($column as $k => $type) {
                            if ($db->_structure[$key][$ke][$k] !== $this->_structure[$key][$ke][$k]){
                                $result[$key][$ke][$k] = $type;
                            }
                        }
                    }
                }
            }
        }
        return isset($result) ? $result : false;
    }

    private function validate()
    {
        if (!($this->_host && $this->_username && $this->_dbName)) {
            $this->_errors[] = "Missed required connection params.";
        }
        $pattern = '/^[a-zA-Z0-9\-\_\.]+$/i';
        if (!preg_match($pattern, $this->_host)) {
            $this->_errors[] = "Param host has prohibited symbols.";
        }
        if (!preg_match($pattern, $this->_username)) {
            $this->_errors[] = "Param username has prohibited symbols.";
        }
        if (!preg_match($pattern, $this->_dbName)) {
            $this->_errors[] = "Param dbName has prohibited symbols.";
        }
        if ($this->_password) {
            if (!preg_match($pattern, $this->_password)) {
                $this->_errors[] = "Param password has prohibited symbols.";
            }
        }
    }
    
    public function getErrors()
    {
        return $this->_errors;
    }
}