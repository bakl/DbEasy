<?php
/**
 * PLACEHOLDER: ?
 *
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 12.09.15
 */

namespace DbEasy\Placeholder;


class Common implements PlaceholderInterface
{

    /**
     * @param $value
     * @return mixed
     */
    public function transformValue($value)
    {
        return $value;
    }

    /**
     * @param $subQuery
     * @return mixed
     */
    public function transformSubQuery($subQuery)
    {

    }

    /**
     * @return string
     */
    public function getName()
    {
        return "s";
    }

    /**
     * @return string
     */
    public function getRegexp()
    {
        return "s";
    }
}