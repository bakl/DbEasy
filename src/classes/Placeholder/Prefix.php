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
        // TODO: Implement transformValue() method.
    }

    /**
     * @param $value
     * @param $nativePlaceholder
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '')
    {
        // TODO: Implement transformPlaceholder() method.
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