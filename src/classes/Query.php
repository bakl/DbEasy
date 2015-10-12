<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 12.09.15
 */

namespace DbEasy;


class Query
{
    /**
     * @var string
     */
    private $queryAsText;

    /**
     * @var array
     */
    private $values;

    /**
     * Query constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param array $array
     * @return Query
     */
    public static function createByArray(array $array)
    {
        $query = new Query();
        $query->queryAsText = $array[0];
        unset($array[0]);
        $query->values = array_values($array);

        return $query;
    }

    /**
     * @param $queryAsString
     * @param $values
     * @return Query
     */
    public static function create($queryAsString, $values)
    {
        $query = new Query();
        $query->queryAsText = $queryAsString;
        $query->values = $values;

        return $query;
    }

    /**
     * @return string
     */
    public function getQueryAsText()
    {
        return $this->queryAsText;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param $index
     * @return mixed
     * @throws DatabaseException
     */
    public function getValue($index)
    {
        if (!isset($this->values[$index])) {
            throw new DatabaseException('not found value with index: '.htmlentities($index));
        }

        return $this->values[$index];
    }

}