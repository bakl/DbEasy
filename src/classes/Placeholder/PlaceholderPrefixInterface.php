<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 17.10.15
 */

namespace DbEasy\Placeholder;


interface PlaceholderPrefixInterface
{
    /**
     * @param $prefix
     * @return void
     */
    public function setPrefix($prefix);

    /**
     * @return string
     */
    public function getPrefix();
}