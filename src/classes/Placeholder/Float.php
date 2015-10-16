<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;


class Float implements PlaceholderInterface
{

    /**
     * @param $value
     * @return mixed
     */
    public function transformValue($value)
    {
        return floatval($value);
    }

    /**
     * @param $value
     * @param string $nativePlaceholder
     * @param string $prefix
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '', $prefix = '')
    {
        return (empty($nativePlaceholder)) ? $value : $nativePlaceholder;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "f";
    }

    /**
     * @return string
     */
    public function getRegexp()
    {
        return "f";
    }
}