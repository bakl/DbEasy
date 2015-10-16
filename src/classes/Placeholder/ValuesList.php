<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;



class ValuesList implements PlaceholderInterface
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
        $values = (empty($nativePlaceholder)) ? $value : array_fill(0, count($value), $nativePlaceholder);
        return implode(",", $values);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "a";
    }

    /**
     * @return string
     */
    public function getRegexp()
    {
        return "a";
    }


}