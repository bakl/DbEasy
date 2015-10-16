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