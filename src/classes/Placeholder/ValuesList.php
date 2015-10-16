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
        //@TODO escape
        return array_values($value);
    }

    /**
     * @param $value
     * @param string $nativePlaceholder
     * @param string $prefix
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '', $prefix = '')
    {
        $resultValues = array();
        foreach ($value as $valueRowKey => $valueRowData) {
            //@TODO escape $valueRowData as value
            //$valueRowData = escape($valueRowData);
            if(!is_int($valueRowKey)){
                //@TODO escape $valueRowKey as identifier
                $resultValues[] = implode("=", array($valueRowKey, (empty($nativePlaceholder)) ? $valueRowData : $nativePlaceholder));
            } else {
                $resultValues[] = (empty($nativePlaceholder)) ? $valueRowData : $nativePlaceholder;
            }
        }

        return implode(",", $resultValues);
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