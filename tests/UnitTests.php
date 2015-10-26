<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 17.10.15
 */

namespace DbEasy\Tests;


class UnitTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('All unit tests');

        $suite->addTestSuite('\DbEasy\Tests\Unit\QueryTransformerTest');
        $suite->addTestSuite('\DbEasy\Tests\Unit\DatabaseTest');

        return $suite;
    }
}