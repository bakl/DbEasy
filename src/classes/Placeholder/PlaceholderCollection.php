<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 16.10.15
 */

namespace DbEasy\Placeholder;


use DbEasy\DatabaseException;

class PlaceholderCollection
{
    /**
     * @var PlaceholderInterface[]
     */
    private $placeholders = array();

    /**
     * @param PlaceholderInterface $placeholder
     * @return void
     */
    public function addPlaceholder(PlaceholderInterface $placeholder)
    {
        $this->placeholders[$placeholder->getName()] = $placeholder;
    }

    /**
     * @param $name
     * @return PlaceholderInterface
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
        $regexp = '';
        /** @var PlaceholderInterface $placeholder */
        foreach ($this->placeholders as $placeholder) {
            $regexp .= $placeholder->getRegexp();
        }
        return $regexp;
    }

}