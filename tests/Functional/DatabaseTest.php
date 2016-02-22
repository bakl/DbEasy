<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 19.10.15
 */

namespace DbEasy\Tests\Functional;


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

CREATE TABLE [x?[x]
(
    [id] INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    [value] NVARCHAR(120)
);

INSERT INTO [x?[x] ([value], [id]) VALUES ('SomeValue', 3);
SQL;

        if ($pdo->exec($sql) === false) {
            $this->fail(var_export($pdo->errorInfo()));
        }
    }

    public function testSelect_QueryWithCommonPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $this->assertEquals("SELECT * FROM Album WHERE ArtistId = '2'",
            $this->db->getQuery('SELECT * FROM Album WHERE ArtistId = ?', 2));
        $result = $this->db->select("SELECT * FROM Album WHERE ArtistId = ?", 2);
        $this->assertEquals($result[0]['Title'], 'Balls to the Wall');
        $this->assertEquals($result[1]['Title'], 'Restless and Wild');
        $this->assertCount(2, $result);
    }

    public function testSelect_QueryWithFloatPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $sqlAsText = $this->db->getQuery("SELECT ?f + ?f + ?f + ?f + ?f + ?f", 10.5, 10, null, 'string', '2string',
            '10');
        $this->assertEquals('SELECT 10.5 + 10 + 0 + 0 + 2 + 10', $sqlAsText);
        $result = $this->db->selectCell("SELECT ?f + ?f + ?f + ?f + ?f + ?f", 10.5, 10, null, 'string', '2string',
            '10');
        $this->assertEquals(32.5, $result);
    }

    public function testSelect_QueryHandleError()
    {
        $isHandleError = false;
        $line = 0;
        $this->db->setErrorHandler(function ($message, $error) use (&$isHandleError, &$line) {
            $isHandleError = true;
            $context = __DIR__ . '/DatabaseTest.php line ' . $line;
            $this->assertEquals('no such column: ERROR_NO_VALUE at ' . $context, $message);
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
        $this->assertTrue($isHandleError);
    }

    public function testSelectCol_QueryWithArrayPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $this->assertEquals(
            "SELECT name FROM Artist WHERE ArtistId IN ('1','2')",
            $this->db->getQuery('SELECT name FROM Artist WHERE ArtistId IN (?a)', [1, 2])
        );
        $result = $this->db->selectCol('SELECT name FROM Artist WHERE ArtistId IN (?a)', [1, 2]);
        $this->assertEquals(array('AC/DC', 'Accept'), $result);
    }

    public function testSelectCell_QueryWithIdentifierPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $this->assertEquals(
            "SELECT name FROM [Artist] WHERE ArtistId = 3",
            $this->db->getQuery('SELECT name FROM ?# WHERE ArtistId = 3', 'Artist')
        );
        $result = $this->db->selectCell('SELECT name FROM ?# WHERE ArtistId = 3', 'Artist');
        $this->assertEquals('Aerosmith', $result);
    }

    public function testSelectCell_QueryWithIntPlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $this->assertEquals(
            'SELECT name FROM Artist WHERE ArtistId = 3',
            $this->db->getQuery('SELECT name FROM Artist WHERE ArtistId = ?d', 3)
        );
        $result = $this->db->selectCell('SELECT name FROM Artist WHERE ArtistId = ?d', 3);
        $this->assertEquals('Aerosmith', $result);
    }

    public function testSelectCell_QueryAssociativePlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $this->assertEquals(
            "SELECT ArtistId FROM Artist WHERE [name] = 'Aerosmith'",
            $this->db->getQuery(
                'SELECT ArtistId FROM Artist WHERE ?a',
                ['name' => 'Aerosmith']
            )
        );
        $this->assertEquals(3, $this->db->selectCell('SELECT ArtistId FROM Artist WHERE ?a', ['name' => 'Aerosmith']));
    }

    public function testSelectCell_QueryWithOptionalBlocks()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });
        $this->assertEquals(
            "SELECT ArtistId FROM Artist WHERE 1 AND ([name] = 'Aerosmith')",
            $this->db->getQuery(
                'SELECT ArtistId FROM Artist WHERE 1 AND ({[name] = ?}{ OR [name] = ?})',
                'Aerosmith',
                Database::SKIP_VALUE
            )
        );
        $this->assertEquals(
            3,
            $this->db->selectCell(
                'SELECT ArtistId FROM Artist WHERE 1 AND ({[name] = ?}{ OR [name] = ?})',
                'Aerosmith',
                Database::SKIP_VALUE
            )
        );
    }

    public function testQuery_InsertQuery()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });

        $id = $this->db->query("INSERT INTO Artist ([name]) VALUES(?)", 'DDT');
        $this->assertNotEmpty($id);

        $artist = $this->db->selectCell("SELECT name FROM Artist WHERE ArtistId = ?", $id);
        $this->assertEquals($artist, 'DDT');
    }

    public function testQuery_SqliteSquareBracketsIgnorePlaceholder()
    {
        $this->db->setErrorHandler(function () {
            $this->fail();
        });

        $result = $this->db->query("SELECT [value] FROM [x?[x] WHERE [id] = ?", 3);
        $this->assertEquals('SomeValue', $result[0]['value']);
    }
}
