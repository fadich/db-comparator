<?php


namespace royal\db\mysql\query;


use royal\base\Interact;
use royal\db\mysql\MySql;

/**
 * Class BaseQueryBuilder
 * @package royal\db\mysql\query
 *
 * @property \mysqli $connection
 */
abstract class BaseQueryBuilder extends Interact
{
    protected $_table;

    /** @var \mysqli $_connection */
    protected $_connection;

    public function __construct(MySql $sql)
    {
        $this->_connection = $sql->connect();
    }

    /**
     * @return \mysqli
     */
    protected function getConnection(): \mysqli
    {
        return $this->_connection;
    }

    public function select($from)
    {

    }
}
