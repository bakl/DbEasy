<?php

use DbEasy\Database;

/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:02
 */
class DbEasyTest extends PHPUnit_Framework_TestCase
{
    /** @var Database $db */
    private $db;

    public function setUp(){
        $this->db = Database::connect("mysql://root:CeRf@127.0.0.1/exercise");
    }

    public function testSimpleQuery()
    {
        $result = $this->db->selectCol("SELECT name FROM human WHERE id = ?", 1);
        var_dump($result);
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
        $this->db->query("UPDATE human SET ?a WHERE id = ?", array('name' => 'George_wrong_name'), 3);
        $this->db->query("UPDATE human SET ?a WHERE id = ?", array('name' => 'George'), 3);

        $result = $this->db->selectCol("SELECT name FROM human WHERE id = ?d", 3);
//        var_dump($result);
        $this->assertEquals(array('George'), $result);
    }

    public function testQueryWithOptionalBlocks()
    {

        $result = $this->db->selectCol(
            "SELECT name
            FROM human
            WHERE
             id = ?d
             {AND id != ? OR id != ?}
             {AND id != ?}", 3, DBEASY_SKIP, 4, 5
        );
        $this->assertEquals(array('George'), $result);
    }

    public function testInsertQuery(){
        $this->db->query("DELETE FROM human WHERE name = 'TestHuman'");

        $id = $this->db->query("INSERT INTO human (`name`, `age`) VALUES(?,?)", 'TestHuman', 25);
        $this->assertNotEmpty($id);

        $insertedHuman = $this->db->selectCell("SELECT name FROM human WHERE id = ?", $id);
        $this->assertEquals($insertedHuman, 'TestHuman');

        $deletedCount = $this->db->query("DELETE FROM human WHERE id = ?", $id);
        $this->assertEquals(1, $deletedCount);
    }


}
