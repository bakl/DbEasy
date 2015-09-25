<?php

use DbEasy\DbEasy;

/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:02
 */
class DbEasyTest extends PHPUnit_Framework_TestCase
{
    /** @var  DbEasy $db */
    private $db;

    public function setUp(){
        $this->db = new DbEasy("mysql://root:CeRf@localhost/exercise");
    }

    public function testSimpleQuery()
    {
        $result = $this->db->selectCol("SELECT name FROM human WHERE id = ?", 1);
//        var_dump($result);
        $this->assertEquals(array('Vasya'), $result);
    }

    public function testQueryWithArrayPlaceHolder()
    {
        $result = $this->db->selectCol(
            "SELECT name FROM human WHERE id IN (?a) AND id != ?",
            array(1,2),
            3
        );

//        var_dump($result);
        $this->assertEquals(array('Vasya', 'Maria'), $result);
    }

    public function testQueryWithIdentifierPlaceHolder()
    {
        $result = $this->db->selectCol(
            "SELECT name FROM ?# WHERE id IN (?a) AND id != ?",
            'human',
            array(1,2),
            3
        );

//        var_dump($result);
        $this->assertEquals(array('Vasya', 'Maria'), $result);
    }

    public function testQueryWithIntPlaceHolder()
    {
        $result = $this->db->selectCol("SELECT name FROM human WHERE id = ?d", 3);

//        var_dump($result);
        $this->assertEquals(array('George'), $result);
    }

    public function testQueryAssociativePlaceHolder()
    {
        $this->db->query("UPDATE human SET ?a WHERE id = ", array('id' => 9999), 3);
        $this->db->query("UPDATE human SET ?a WHERE id = ", array('id' => 3), 3);

        $result = $this->db->selectCol("SELECT name FROM human WHERE id = ?d", 3);
//        var_dump($result);
        $this->assertEquals(array('George'), $result);
    }
}
