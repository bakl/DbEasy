<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */
namespace DbEasy\Placeholder;

use DbEasy\DatabaseException;
use DbEasy\QuotePerformerInterface;

abstract class PlaceholderAbstract
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var QuotePerformerInterface
     */
    private $quotePerformer;

    /**
     * @param $value
     * @return mixed
     */
    abstract public function transformValue($value);

    /**
     * @param $value
     * @param string $nativePlaceholder
     * @return string
     */
    abstract public function transformPlaceholder($value, $nativePlaceholder = '');

    /**
     * @return QuotePerformerInterface
     */
    public function getQuotePerformer()
    {
        return $this->quotePerformer;
    }

    /**
     * @param QuotePerformerInterface $quotePerformer
     */
    public function setQuotePerformer(QuotePerformerInterface $quotePerformer)
    {
        $this->quotePerformer = $quotePerformer;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @throws DatabaseException
     */
    protected function setName($name)
    {
        if (!empty($this->name)) {
            throw new DatabaseException('placeholder name error: reinitialization not available');
        }

        if (!preg_match('~^\?[a-z_#]?$~', $name)) {
            throw new DatabaseException('placeholder name error: wrong format');
        }

        $this->name = $name;
    }
}