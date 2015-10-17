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
}