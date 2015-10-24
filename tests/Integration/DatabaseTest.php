<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 19.10.15
 */

namespace DbEasy\Tests\Integration;


use DbEasy\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Database $db */
    private $db;

    public function setUp()
    {
        $this->db = Database::connect("sqlite::memory:");
        $this->db->getAdapter()->connect();
        /** @var \PDO $pdo */
        $pdo = $this->db->getAdapter()->getConnection();
        $sql = <<<SQL
CREATE TABLE [Album]
(
    [AlbumId] INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    [Title] NVARCHAR(160)  NOT NULL,
    [ArtistId] INTEGER  NOT NULL,
    FOREIGN KEY ([ArtistId]) REFERENCES [Artist] ([ArtistId])
		ON DELETE NO ACTION ON UPDATE NO ACTION
);

CREATE TABLE [Artist]
(
    [ArtistId] INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    [Name] NVARCHAR(120)
);

INSERT INTO [Artist] ([Name]) VALUES ('AC/DC');
INSERT INTO [Artist] ([Name]) VALUES ('Accept');
INSERT INTO [Artist] ([Name]) VALUES ('Aerosmith');
INSERT INTO [Artist] ([Name]) VALUES ('Alanis Morissette');

INSERT INTO [Album] ([Title], [ArtistId]) VALUES ('For Those About To Rock We Salute You', 1);
INSERT INTO [Album] ([Title], [ArtistId]) VALUES ('Balls to the Wall', 2);
INSERT INTO [Album] ([Title], [ArtistId]) VALUES ('Restless and Wild', 2);
INSERT INTO [Album] ([Title], [ArtistId]) VALUES ('Let There Be Rock', 1);
INSERT INTO [Album] ([Title], [ArtistId]) VALUES ('Big Ones', 3);
SQL;

        $pdo->exec($sql);
    }

    public function testSelect_QueryWithCommonPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $result = $this->db->select("SELECT * FROM Album WHERE ArtistId = ?", 2);
        $this->assertEquals($result[0]['Title'], 'Balls to the Wall');
        $this->assertEquals($result[1]['Title'], 'Restless and Wild');
        $this->assertCount(2, $result);
        $this->db->setErrorHandler(null);
    }

    public function testSelect_QueryWithFloatPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $result = $this->db->selectCell("SELECT ?f + ?f + ?f + ?f + ?f + ?f", 10.5, 10, NULL, 'string', '2string', '10');
        $sqlAsText = $this->db->getQuery("SELECT ?f + ?f + ?f + ?f + ?f + ?f", 10.5, 10, NULL, 'string', '2string', '10');
        $this->assertEquals(32.5, $result);
        $this->assertEquals('SELECT 10.5 + 10 + 0 + 0 + 2 + 10', $sqlAsText);
        $this->db->setErrorHandler(null);
    }

    public function testSelect_QueryHandleError()
    {
        $isHandleError = false;
        $line = 0;
        $this->db->setErrorHandler(function ($message, $error) use (&$isHandleError, &$line) {
            $isHandleError = true;
            $context = '/Users/dakulov/projects/mine/DbEasy/DbEasy/tests/Integration/DatabaseTest.php line '.$line;
            $this->assertEquals('no such column: ERROR_NO_VALUE at '.$context, $message);
            $this->assertEquals(
                [
                    'code' => 1,
                    'message' => 'no such column: ERROR_NO_VALUE',
                    'query' => 'SELECT 2 + ERROR_NO_VALUE',
                    'context' => $context
                ],
                $error
            );
        });

        $line = __LINE__ + 1;
        $this->db->select("SELECT ?f + ?f", 2);
        $this->db->setErrorHandler(null);
        $this->assertTrue($isHandleError);
    }

}
