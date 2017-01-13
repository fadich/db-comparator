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
    protected $_selecting;
    protected $_join;

    /** @var MySql $_connection */
    protected $_connection;

    public function __construct(MySql $sql)
    {
        $this->_connection = $sql->connect();
    }

    /**
     * @return \mysqli
     */
    protected function getConnection() : \mysqli
    {
        return $this->_connection->connector;
    }

    public function select($columns)
    {
        $this->_selecting = QueryHelper::columns($columns);
    }

    public function from(string $from, string $alias = '')
    {
        $this->_table = "`{$this->_connection->database}`.`{$from}`" . ($alias ? "`{$alias}` " : " ");
    }

    /**
     * Entry join-data in array.
     * Will be created as:
     *      INNER JOIN $table [ON $conditions].
     *
     * @param string       $table         Join table name
     * @param array|string $conditions    Join conditions
     *
     * @return static
     */
    public function innerJoin(string $table, $conditions = [])
    {
        $this->_join[] = ['type' => 'INNER', 'table' => $table, 'conditions' => $conditions];
        return $this;
    }

    /**
     * Entry join-data in array.
     * Creating query's LEFT JOIN.
     * For example:
     *      LEFT JOIN $table [ON $conditions].
     *
     * @param string       $table         Join table name
     * @param array|string $conditions    Join conditions
     *
     * @return static
     */
    public function leftJoin(string $table, $conditions = [])
    {
        $this->_join[] = ['type' => 'LEFT', 'table' => $table, 'conditions' => $conditions];
        return $this;
    }

    /**
     * Counting the number of pages of the table.
     * For example:
     *    using with conditions 'item_id < 110' -- returns the page number on which the element with item_id 110;
     *
     * @param int          $pageSize   the number of the elements on page
     * @param string|array $conditions selecting conditions (WHERE-params)
     *
     * @return int
     */
    public function countPages(int $pageSize, $conditions = []) : int
    {
        $where  = QueryHelper::where($conditions);
        $joins  = QueryHelper::joins($this->_join);
        $number = (int)$this->_connection->createCommand("SELECT FLOOR(COUNT(*) / {$pageSize}) AS `pages` FROM {$this->_name} {$joins} {$where}")->queryScalar();
        return $number ? $number : 1;
    }
}
