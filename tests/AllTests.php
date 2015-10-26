<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 17.10.15
 */

namespace DbEasy\Tests;


class AllTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('All tests');

        $suite->addTestSuite(UnitTests::suite());
        $suite->addTestSuite(FunctionalTests::suite());

        return $suite;
    }
}