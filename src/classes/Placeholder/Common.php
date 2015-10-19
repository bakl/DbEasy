<?php
/**
 * PLACEHOLDER: ?
 *
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 12.09.15
 */

namespace DbEasy\Placeholder;


class Common extends PlaceholderAbstract
{
    /**
     * constructor is required for initialization placeholder name
     */
    public function __construct()
    {
        $this->setName('?');
    }

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
     * @param string $nativePlaceholder
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '')
    {
        if (!empty($nativePlaceholder)) {
            return $nativePlaceholder;
        }

        return $this->getQuotePerformer()->quote($value);
    }
}