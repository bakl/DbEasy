<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;


class Prefix implements PlaceholderInterface
{

    /**
     * @param $value
     * @return mixed
     */
    public function transformValue($value)
    {
        return null;
    }

    /**
     * @param $value
     * @param string $nativePlaceholder
     * @param string $prefix
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '', $prefix = '')
    {
        //@TODO need escape with other part of table, this is a problem captian
        return $prefix;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "_";
    }

    /**
     * @return string
     */
    public function getRegexp()
    {
        return "_";
    }


}