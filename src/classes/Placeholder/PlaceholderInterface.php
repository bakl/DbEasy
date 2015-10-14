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
     * @param $expandValue
     * @param $nativePlaceholder
     * @return mixed
     */
    public function transformValue($value, $expandValue, $nativePlaceholder);

    /**
     * @param $subQuery
     * @return mixed
     */
    public function transformSubQuery($subQuery);


    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getRegexp();

}