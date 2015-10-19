<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 15.10.15
 */

namespace DbEasy\Tests\Unit;


use DbEasy\Database;
use DbEasy\DSN;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    public function testGetAdapter_BySchemeSqlite()
    {
        $db = new Database(new DSN('sqlite:'));
        $this->assertInstanceOf('\DbEasy\Adapter\Sqlite', $db->getAdapter());
    }

    public function testGetAdapter_BySchemeMysql()
    {
        $db = new Database(new DSN('mysql:'));
        $this->assertInstanceOf('\DbEasy\Adapter\Mysql', $db->getAdapter());
    }

    public function testGetAdapter_UserAdapter()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $db = new Database(new DSN('test:'));
        $db->setAdapter($myAdapter);
        $this->assertEquals($myAdapter, $db->getAdapter());
    }

    public function testAddCustomPlaceholder_setCustomsPlaceholdersAndDefaultAdapter()
    {
        $db = new Database(new DSN('mysql:'));
        $myPlaceholderOne = Helper::getMockCustomPlaceholder('?x');
        $db->addCustomPlaceholder($myPlaceholderOne);
        $myPlaceholderTwo = Helper::getMockCustomPlaceholder('?y');
        $db->addCustomPlaceholder($myPlaceholderTwo);

        $this->assertInstanceOf('\DbEasy\Adapter\Mysql', $db->getAdapter());
        $placeholders = $db->getPlaceholders();
        foreach ($db->getPlaceholders()->getAllPlaceholdersAsArray() as $placeholder) {
            $this->assertInstanceOf('\DbEasy\Adapter\Mysql', $placeholder->getQuotePerformer());
        }

        $this->assertEquals('f#_nadxy', $placeholders->getAllPlaceholdersAsString());
    }

    public function testAddCustomPlaceholder_setCustomsPlaceholdersSetCustomAdapter()
    {
        $db = new Database(new DSN('mysql:'));
        $myPlaceholderOne = Helper::getMockCustomPlaceholder('?x');
        $db->addCustomPlaceholder($myPlaceholderOne);
        $myAdapter = Helper::getMockCustomAdapter();
        $db->setAdapter($myAdapter);
        $myPlaceholderTwo = Helper::getMockCustomPlaceholder('?y');
        $db->addCustomPlaceholder($myPlaceholderTwo);


        $this->assertEquals($myAdapter, $db->getAdapter());
        $placeholders = $db->getPlaceholders();
        foreach ($db->getPlaceholders()->getAllPlaceholdersAsArray() as $placeholder) {
            $this->assertEquals($myAdapter, $placeholder->getQuotePerformer());
        }

        $this->assertEquals('f#_nadxy', $placeholders->getAllPlaceholdersAsString());
    }
}