<?php

namespace comparator\core;


use royal\base\Application;
use royal\base\console\Console;
use royal\base\Object;
use royal\db\mysql\exception\MySqlRequestError;
use royal\db\mysql\MySql;
use royal\db\mysql\query\QueryBuilder;

/**
 * Class DbComparator
 *
 * @package     Comparator
 *
 *
 * @property array $errors
 * @property string $host
 * @property string $username
 * @property string $database
 * @property string $password
 * @property bool   $isEmpty
 */
class DbComparator extends Object
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
        $this->_host     = $host; // ?: 'localhost';
        $this->_username = $username;// ?: 'root';
        $this->_dbName   = $dbName;// ?: 'mysql';
        $this->_password = $password;
        $this->validate();
        try {
            $this->connection();
            $this->getTables();
        } catch (\mysqli_sql_exception $e) {
            $this->_errors[] = $e;
            throw $e;
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
        try {
            $this->_database = new \mysqli($this->_host, $this->_username, $this->_password, $this->_dbName);
        } catch (\mysqli_sql_exception $e) {
            $this->_errors[]   = 'Error connection to ' . $this->_username . '@' . $this->_host;
            throw new \Exception('Error connection to ' . $this->_username . '@' . $this->_host);
        }
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

    public function join(DbComparator $joining)
    {
        if ($this->compare($joining)) {
            return $this->dumpBase($joining);
//            foreach ($diff as $table => $columns) {
//                if (is_string($columns)) {
//                    $this->createTable($columns, $joining);
//                } elseif (is_array($columns)) {
//                    //alter table
//                    foreach ($columns as $name => $column) {
//                        if (is_string($column)) {
//                        // creating column
//                        } elseif (is_array($column)) {
//                            // alter column
//                            foreach ($column as $prop => $type) {
//                                if (is_string($type)) {
//                                    // alter type
//                                }
//                            }
//                        }
//                    }
//                }
//            }
        }
        return true;
    }

    protected function getErrors()
    {
        return $this->_errors;
    }

    protected function getHost()
    {
        return $this->_host;
    }

    protected function getUsername()
    {
        return $this->_username;
    }

    protected function getDatabase()
    {
        return $this->_dbName;
    }

    protected function getPassword()
    {
        return $this->_password;
    }

    protected function createTable($tableName, DbComparator $updating)
    {
        $table   = $this->_structure[$tableName];

        $columns = $this->columnStructure($table);
        $keys    = $this->getTableKeys($table);
        $params  = "";
        $request = "CREATE TABLE IF NOT EXISTS `{$tableName}` ( {$columns} " . ($keys ? ", {$keys}" : " ") . ") {$params} ";
        $sql     = new MySql($updating->host, $updating->username, $updating->database, $updating->password);
        $query   = new QueryBuilder($sql);

        echo '<pre>'; var_dump($query); die;
        if (!$query->connection->query($request)) {
            throw new MySqlRequestError($query->connection->error);
        } else {
            var_export("success");
        }
        die;
        return true;
    }

    protected function columnStructure(array $table)
    {
        $res = "";
        $i   = sizeof($table);
        foreach ($table as $column => $properties) {
            $res .= "`{$column}` " . $properties['Type'];
            $res .= $properties['Null'] === "NO" ? " NOT NULL " : " ";
            $res .= "DEFAULT " . ($properties['Default'] ? var_export($properties['Default']) : "NULL ") . (--$i ? ", " : " ");
            // TODO: !!! EXTRA !!!
        }
        return $res;
    }

    protected function getTableKeys(array $table)
    {
        $keys = [];
        foreach ($table as $column => $properties) {
            foreach ($properties as $name => $property) {
                if ($name == "Key") {
                    if ($property == "PRI") {
                        $keys[] = " PRIMARY KEY (`{$column}`)";
                    }
                }
            }
        }
        return implode(", ", $keys);
    }

    protected function dumpBase(DbComparator $updating)
    {
        $file = Application::basePath() . "/dumps/" . time() . ".sql";
        (new Console())->mysqldump(
            "--no-data",
            "--user='{$this->username}'",
            $this->password ? " --password='{$this->password}' " : "",
            "--host={$this->host}",
            $this->database,
            "> {$file}"
        )->execute;
        (new Console())->mysql(
            "--user='{$updating->username}'",
            $updating->password ? " --password='{$updating->password}' " : "",
            "--host={$updating->host}",
            $updating->database,
            "< {$file}"
        )->execute;
        unlink($file);
        return true;
    }

    protected function getIsEmpty()
    {
        return (bool)$this->getContent();
    }
}