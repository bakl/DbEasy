<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;


class Identifier extends PlaceholderAbstract
{
    /**
     * constructor is required for initialization placeholder name
     */
    public function __construct()
    {
        $this->setName('?#');
    }

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
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '')
    {
        return $this->getQuotePerformer()->quoteIdentifier($value);
    }
}