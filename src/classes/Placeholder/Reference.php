<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;



class Reference implements PlaceholderInterface
{

    /**
     * @param $value
     * @return mixed
     */
    public function transformValue($value)
    {
        // TODO: Implement transformValue() method.
    }

    /**
     * @param $subQuery
     * @return mixed
     */
    public function transformSubQuery($subQuery)
    {
        // TODO: Implement transformSubQuery() method.
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "n";
    }

    /**
     * @return string
     */
    public function getRegexp()
    {
        return "n";
    }
}