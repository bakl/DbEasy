<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;


class Float extends PlaceholderAbstract
{
    /**
     * constructor is required for initialization placeholder name
     */
    public function __construct()
    {
        $this->setName('?f');
    }

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
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '')
    {
        if (!empty($nativePlaceholder)) {
            return $nativePlaceholder;
        }

        return floatval($value);
    }
}