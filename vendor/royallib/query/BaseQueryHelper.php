<?php

namespace royal\query;


use royal\base\Object;

/**
 * Class BaseQueryHelper
 * @package app\components\HelpClasses\query
 *
 * Providing tools for building SQL-queries.
 *
 * Base abstract class, that can provide some static methods.
 * Basically, i think, there will be much of static methods, that build some parts of query (string).
 *
 * @author Fadi Ahmad
 */
abstract class BaseQueryHelper extends Object
{
    /**
     * Usually, there is no camelCase into SQL databases.
     * This method using for making some string (for example php-class's attribute's name)
     *    from camelCaseString to string, that uses delimiter between words.
     *
     * For example, "usingUnderscoreString" (using $replacement = '_$0') will be converted to "using_underscore_string".
     *
     * @param string $str
     *
     * @return string
     */
    public static function fromCamelCase(string $str) : string 
    {
        return (string)strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    /**
     * Creating WHERE-params string from array or string.
     * For example:
     *      a) $conditions array can be set as ['param_1' => 'value_1', 'param_2 !' => 'value_2']. In this case, there will be
     *         conditions of selecting query such as "WHERE param_1=value_1 AND param_2 !=value_2";
     *      b) condition string can be set as "param_1 = 'val_1' OR param_2 > 30". In this case, there will be
     *         created conditions of selecting query such as "WHERE param_1 = 'val_1' OR param_2 > 30".
     *
     * In other words, conditions, that expressed as an array, permit to select data with exact match;
     * but string expressed conditions can be used to get data by any SQL conditions.
     *
     * @param array|string $conditions
     *
     * @return string
     */
    public static function where($conditions) : string
    {
        return is_array($conditions) ? self::arrayToParams($conditions, 'WHERE', 'AND', '=', true) : ($conditions ? "WHERE " . $conditions : " ");
    }

    /**
     * Creating ON-condition string from array.
     * For example:
     *      $conditions array can be set as ['param_1' => 'value_1', 'param_2 !' => 'value_2']. In this case, there will be
     *      conditions of joining such as "ON param_1=value_1 AND param_2 !=value_2".
     *
     * @param array|string $conditions
     *
     * @return string
     */
    public static function on($conditions) : string
    {
        return is_array($conditions) ? self::arrayToParams($conditions, 'ON', 'AND', '=') : ($conditions ? "ON {$conditions}" : " ");
    }

    /**
     * Creating ORDER BY string from array.
     * For example:
     *      $order array can be set as ['column_1' => 'ASC', 'column_2' => 'DESC']. In this case, there will be
     *      ordering in selecting query such as "ORDER BY column_1 ASC, column_2 DESC".
     *
     * @param array $array
     *
     * @return string
     */
    public static function order(array $array) : string
    {
        return self::arrayToParams($array, 'ORDER BY', ', ', ' ');
    }

    /**
     * Creating LIMIT-param string from array.
     * For example:
     *      $limit array can be set as [0,30]; in this case, there will be limit param in selecting query
     *          such as "LIMIT 0,30";
     *      if $limit array set as [30], in this case, there will be interpreted as "LIMIT 30".
     *
     * Limit array max size is 2.
     *
     * @param array $array
     *
     * @return string
     */
    public static function limit(array $array) : string
    {
        if (sizeof($array) > 2) {
            throw new \InvalidArgumentException("Limit array may contain no more, than 2 elements");
        }
        return !empty($array) ? "LIMIT " . implode(",", $array) : " ";
    }

    /**
     * Whenever selecting columns array is empty, then selecting all (return "*"),
     *      else, array elements (that are strings names of necessary columns) separated by a comma.
     * In case of the is string a key of the array element, this means that value will an selecting column alias.
     * For example,
     *
     * ```php
     * echo BaseQueryHelper::columns([
     *      'column_1',
     *      'column_2',
     *      'something' => 'column_3',
     *      'foo'       => 'column_4',
     *      'column_5',
     * ]);
     * // The result string will be:
     * // "column_1, column_2, something AS column_3, foo AS column_4, column_5 "
     *
     * ```
     *
     * @param array $array
     *
     * @return string
     */
    public static function columns(array $array) : string
    {
        if (empty($array)) {
            return " * ";
        }
        $columns = " "; $len = sizeof($array);
        foreach ($array as $key => $item) {
            if (is_string($key)) {
                $columns .= " {$key} AS ";
            }
            $columns .= " {$item}" . (--$len ? ", " : " ");
        }
        return $columns;
    }

    /**
     * Creating query-string (part of query) from some array params.
     * @see BaseQueryHelper::where()
     * @see BaseQueryHelper::on()
     * @see BaseQueryHelper::order()
     *
     * @param array  $array         the array of parameters
     *                                  such us ['param_1' => 'value_1', 'param_2 !' => 'value_2']
     * @param string $param         the param name (for example, "WHERE")
     * @param string $delimiter     the param delimiter ("AND", "," etc.)
     * @param string $separator     the separator between key and value ("=", " " etc.)
     * @param bool   $toStringValue flag, that means, need to add quotes (for value)
     *
     * @return string
     */
    protected static function arrayToParams(array $array, string $param, string $delimiter, string $separator, bool $toStringValue = false) : string
    {
        $params = " ";
        $quote = $toStringValue ? "\"" : '';
        if ($len = sizeof($array)) {
            $params = " {$param} ";
            foreach ($array as $column => $value) {
                $value = is_string($value) ? "{$quote}{$value}{$quote}" : $value;
                $params .= " {$column}{$separator}{$value} " . (--$len ? " {$delimiter} " : "");
            }
        }
        return $params;
    }

    /**
     * Creating joins (sting, for query) with tables from $this->_join property.
     * 
     * For example:
     * 
     * ```php
     *  // property is array such as
     *  $this->_join =  [
     *                      'type'       => 'inner',
     *                      'table'      => 'table_2',
     *                      'conditions' => [
     *                          'table_1.col_1' => 'table_2.col_1',
     *                      ],
     *                  ]
     *
     *  // will be interpreted to string like:
     *
     *  echo $this->joins();
     *
     *  // output: "INNER JOIN table_2 ON table_1.col_1 = table_2.col_1";
     * ```
     * 
     * @param array $joins array kile ['type' => $joinType, 'table' => $joinTableName, 'conditions' => [$cul_1 => $cil_2]]
     * 
     * @return string
     */
    protected static function joins(array $joins) : string
    {
        $res = " ";
        foreach ($joins as $join) {
            $res .= strtoupper($join['type']) . " JOIN {$join['table']} " . self::on($join['conditions']) . "\n";
        }
        return $res;
    }
}
