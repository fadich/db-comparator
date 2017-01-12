<?php

namespace royal\type;

/**
 * Class Mixed
 * @package app\components\HelpClasses\type
 *
 * Helper for working with any types of variables (mixed).
 *
 * @property array $value       The value (of array).
 * @see Mixed::getValue()
 *
 * @author Fadi Ahmad
 */
class Mixed extends BaseType
{
    /** @var mixed $value */
    protected $_value;

    /**
     * Mixed constructor.
     *
     * Init the value property by any mixed value.
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->_value = $value;
        parent::__construct();
    }

    /**
     * Method works according to principle implode() (converts array to string), except for a few nuances.
     * At first, a string of the converted array also will contains an array keys, previous of elements,
     *      separated from them by some separator.
     * As well, there could be listed the keys of the array, that filtering on imploding.
     *
     * For example:
     * 
     * ```php
     *  // calling on:
     *      $this->_value = array ['el_1' => 1, 'el_2' => 2, 'el_3' => 3];
     * 
     *  // with arguments:
     *      implodeElements('&', ['el_1', 'el_2']);
     * 
     *  // will make a string as: "el_1=1&el_2=2";
     * ```     
     *
     * @param string $glue       a symbol or string that will be placed between the imploded elements;
     * @param array  $keys       an array keys, that should be used (filtered),
     *                               in case of there is no specified keys, all keys will be used;
     * @param string $separator  a symbol or string that is the separator between a keys and an elements;
     * @param bool   $convert    a value ($this->_value) can be convented to array;
     *
     * @return $this
     * @throws \TypeError
     */
    public function implodeElements($glue = "", array $keys = [], string $separator = "=", bool $convert = false)
    {
        if (!$convert && !is_array($this->_value)) {
            throw new \TypeError("Value should be type of array, but not " . gettype($this->_value));
        }
        try {
            $this->_value = (array)$this->_value;
        } catch (\Throwable $e) {
            throw new \TypeError("Value (with type " . gettype($this->_value) . ") cannot be converted to array ");
        }
        $string = ""; $i = 0;
        foreach ($this->_value as $key => $item) {
            if (!empty($keys)) {
                if (in_array($key, $keys)) {
                    $string .= ($i++ ? $glue : "") . "{$key}{$separator}{$item}";
                }
            } else {
                $string .= ($i++ ? $glue : "") . "{$key}{$separator}{$item}";
            }
        }
        $this->_value = $string;
        return $this;
    }

    /**
     * Getting $value property.
     * @see Mixed::$value
     *
     * @return array
     */
    protected function getValue()
    {
        return $this->_value;
    }
}
