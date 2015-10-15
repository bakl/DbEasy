<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 15.10.15
 */

namespace DbEasy\Tests;


use DbEasy\Database;
use DbEasy\DSN;
use DbEasy\Query;

class DatabaseTests extends \PHPUnit_Framework_TestCase
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
        $myAdapter = $this->getMockForAbstractClass('\DbEasy\Adapter\AdapterAbstract');
        $db = new Database(new DSN('test:'));
        $db->setAdapter($myAdapter);
        $this->assertEquals($myAdapter, $db->getAdapter());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValue()
    {
        $myAdapter = $this->getMockCustomAdapter();
        $myPlaceholder = $this->getMockCustomPlaceholder();

        $myPlaceholder->expects($this->any())
            ->method('transformValue')
            ->will($this->returnValue('out'));

        $myPlaceholder->expects($this->any())
            ->method('transformPlaceholder')
            ->will($this->returnValue('_TRANSFORM_PLACEHOLDER_RESULT_'));

        $db = new Database(new DSN('test:'));
        $db->setAdapter($myAdapter);
        $db->setPlaceholder($myPlaceholder);

        $result = $db->transformQuery(Query::create('SQL_TEXT "?m", ?m SQL_TEXT', ['in']));

        $this->assertEquals('SQL_TEXT "?m", _TRANSFORM_PLACEHOLDER_RESULT_ SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals(['out'], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithExpandValue()
    {
        $myAdapter = $this->getMockCustomAdapter();
        $myPlaceholder = $this->getMockCustomPlaceholder();

        $myPlaceholder->expects($this->any())
            ->method('transformValue')
            ->will($this->returnValue('out'));

        $myPlaceholder->expects($this->any())
            ->method('transformPlaceholder')
            ->with($this->equalTo('in'),$this->equalTo(''))
            ->will($this->returnValue('"out"'));

        $db = new Database(new DSN('test:'));
        $db->setAdapter($myAdapter);
        $db->setPlaceholder($myPlaceholder);

        $result = $db->transformQuery(Query::create('SQL_TEXT "?m", ?m SQL_TEXT', ['in']), true);

        $this->assertEquals('SQL_TEXT "?m", "out" SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals([], $result->getValues());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockCustomAdapter()
    {
        $myAdapter = $this->getMockForAbstractClass('\DbEasy\Adapter\AdapterAbstract');
        $myAdapter->expects($this->any())
            ->method('getRegexpForIgnorePlaceholder')
            ->will($this->returnValue('" (?> [^"\\\\]+|\\\\"|\\\\)* "'));
        return $myAdapter;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockCustomPlaceholder()
    {
        $myPlaceholder = $this->getMockForAbstractClass('\DbEasy\Placeholder\PlaceholderInterface');
        $myPlaceholder->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('m'));

        $myPlaceholder->expects($this->any())
            ->method('getRegexp')
            ->will($this->returnValue('m'));
        return $myPlaceholder;
    }
}