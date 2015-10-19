<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 27.09.15
 */

namespace DbEasy\Placeholder;


class Prefix extends PlaceholderAbstract implements PlaceholderPrefixInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * constructor is required for initialization placeholder name
     */
    public function __construct()
    {
        $this->setName('?_');
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
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
        $prefix = $this->getQuotePerformer()->quoteIdentifier($this->getPrefix());
        $quote = mb_substr($prefix, 0, 1);
        return trim($prefix, $quote);
    }
}