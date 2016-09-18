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
    private $_errors = [];
    private $_contents = [];

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
            $this->_errors[] = $e;
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
            $res = $this->_database->query("SHOW COLUMNS FROM " . $item[0])->fetch_all();
            foreach ($res as $re) {
                $this->_contents[$item[0]][$re[0]]['Type']     = $re[1];
                $this->_contents[$item[0]][$re[0]]['Null']     = $re[2];
                $this->_contents[$item[0]][$re[0]]['Key']      = $re[3];
                $this->_contents[$item[0]][$re[0]]['Default']  = $re[4];
                $this->_contents[$item[0]][$re[0]]['Extra']    = $re[5];
            }
        }
    }

    public function getContent()
    {
        return !empty($this->_contents) ? $this->_contents : false;
    }
    
    /** @return bool */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    public function getDbName()
    {
        return $this->_dbName;
    }
    
    public function compare(DbComparator $db)
    {
        foreach ($this->_contents as $key => $table){
            if (!isset($db->_contents[$key])) {
                $result[$key] = $key;
            } else {
                foreach ($table as $ke => $column) {
                    if (!isset($db->_contents[$key][$ke])){
                        $result[$key][$ke] = $ke;
                    } else {
                        foreach ($column as $k => $type) {
                            if ($db->_contents[$key][$ke][$k] !== $this->_contents[$key][$ke][$k]){
                                $result[$key][$ke][$k] = $type;
                            }
                        }
                    }
                }
            }
        }
        return isset($result) ? $result : false;
    }
}