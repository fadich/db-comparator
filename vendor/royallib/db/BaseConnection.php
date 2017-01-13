<?php

namespace royal\db;

// TODO: describe \royal\db\BaseConnection

use royal\base\Interact;

abstract class BaseConnection extends Interact
{
    protected $_host;
    protected $_dbName;
    protected $_username;
    protected $_password;

    abstract function __construct($host = null, $username = null, $dbName = null, $password = null);

    abstract public function getContents();

    abstract protected function connect();

    abstract protected function close();

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
}
