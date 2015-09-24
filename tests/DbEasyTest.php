<?php

use DbEasy\DbEasy;

/**
 * User: sergeymartyanov
 * Date: 25.09.15
 * Time: 0:02
 */
class DbEasyTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleQuery()
    {
//        $db = new DbEasy("mysql://root:CeRf@localhost/exercise");
//        $result = $db->selectCol("SELECT id FROM human WHERE id = ?", 1);
        $this->assertTrue(true);
    }

    public function testQueryWithArrayPlaceHolder()
    {
        $db = new DbEasy("mysql://root:CeRf@localhost/exercise");
        $result = $db->selectCol("SELECT id FROM human WHERE id IN (?a) AND id IN (?a) AND id != ? AND id != ?", array(1), array(2), 1, 2);
        $this->assertTrue(true);
    }
}
