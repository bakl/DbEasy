<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 18.10.15
 */

namespace DbEasy;


interface QuotePerformerInterface
{
    /**
     * @param mixed $value
     * @return string
     */
    public function quote($value);

    /**
     * @param mixed $value
     * @return string
     */
    public function quoteIdentifier($value);
}