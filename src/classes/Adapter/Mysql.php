<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */

namespace DbEasy\Adapter;

use DbEasy\Adapter\AdapterAbstract;
use DbEasy\Query;

class Mysql extends AdapterAbstract
{
    /**
     * @return mixed
     */
    protected function connect()
    {
        // TODO: Implement connection() method.
    }

    /**
     * @param Query $query
     * @return mixed
     */
    protected function executeQuery(Query $query)
    {
        // TODO: Implement _execute() method.
    }

    public function getRegexpForIgnorePlaceholder()
    {
        return '
            "   (?> [^"\\\\]+|\\\\"|\\\\)*    "   |
            \'  (?> [^\'\\\\]+|\\\\\'|\\\\)* \'   |
            `   (?> [^`]+ | ``)*              `   |   # backticks
            /\* .*?                          \*/      # comments
        ';
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function quote($value)
    {
        // TODO: Implement quote() method.
    }
}