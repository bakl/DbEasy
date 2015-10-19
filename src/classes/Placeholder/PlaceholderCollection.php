<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 16.10.15
 */

namespace DbEasy\Placeholder;


use DbEasy\DatabaseException;
use DbEasy\QuotePerformerInterface;

class PlaceholderCollection
{
    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @var PlaceholderAbstract[]
     */
    private $placeholders = array();

    /**
     * @param PlaceholderAbstract $placeholder
     * @return void
     */
    public function addPlaceholder(PlaceholderAbstract $placeholder)
    {
        if ($placeholder instanceof PlaceholderPrefixInterface) {
            $placeholder->setPrefix($this->getPrefix());
        }

        $this->placeholders[$placeholder->getName()] = $placeholder;
    }

    /**
     * @param $name
     * @return PlaceholderAbstract
     * @throws DatabaseException
     */
    public function getPlaceholder($name)
    {
        if (empty($this->placeholders[$name])) {
            throw new DatabaseException("Placeholder ?" . $name . " not found");
        }

        return $this->placeholders[$name];
    }

    /**
     * return void
     */
    public function addDefaultPlaceholders()
    {
        $this->addPlaceholder(new Common());
        $this->addPlaceholder(new Float());
        $this->addPlaceholder(new Identifier());
        $this->addPlaceholder(new Prefix());
        $this->addPlaceholder(new Reference());
        $this->addPlaceholder(new ValuesList());
        $this->addPlaceholder(new WholeNumber());
    }

    /**
     * @return string
     */
    public function getAllPlaceholdersAsString()
    {
        $result = '';
        foreach ($this->placeholders as $placeholder) {
            $result .= preg_quote(ltrim($placeholder->getName(), '?'));
        }

        return $result;
    }

    /**
     * @return PlaceholderAbstract[]
     */
    public function getAllPlaceholdersAsArray()
    {
        return $this->placeholders;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        foreach ($this->placeholders as $placeholder) {
            if ($placeholder instanceof PlaceholderPrefixInterface) {
                $placeholder->setPrefix($prefix);
            }
        }

        echo "a";

        $this->prefix = $prefix;
    }

    /**
     * @param QuotePerformerInterface $quotePerformer
     */
    public function setQuotePerformer(QuotePerformerInterface $quotePerformer)
    {
        foreach ($this->placeholders as $placeholder) {
            $placeholder->setQuotePerformer($quotePerformer);
        }
    }

}