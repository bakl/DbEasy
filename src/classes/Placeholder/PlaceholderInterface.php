<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @author: Sergey Martyanov <s-m-box@ya.ru>
 * @since: 10.09.15
 */
namespace DbEasy\Placeholder;

interface PlaceholderInterface
{
    /**
     * @param $value
     * @return mixed
     */
    public function transformValue($value);


    /**
     * @param $value
     * @param $nativePlaceholder
     * @return string
     */
    public function transformPlaceholder($value, $nativePlaceholder = '');


    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRegexp();

}