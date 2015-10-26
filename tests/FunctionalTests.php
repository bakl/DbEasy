<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 26.10.15
 */

namespace DbEasy\Tests;


class FunctionalTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('All integration tests');

        $suite->addTestSuite('DbEasy\Tests\Functional\DatabaseTest');

        return $suite;
    }
}