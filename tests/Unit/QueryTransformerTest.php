<?php
/**
 * @author: Denis Akulov <akulov.d.g@gmail.com>
 * @since: 17.10.15
 */

namespace DbEasy\Tests\Unit;


use DbEasy\Database;
use DbEasy\Placeholder\PlaceholderCollection;
use DbEasy\Query;
use DbEasy\QueryTransformer;

class QueryTransformerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRegexpMain_GetCorrectRegexp()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $myPlaceholders = new PlaceholderCollection();
        $myPlaceholders->addPlaceholder(Helper::getMockCustomPlaceholder('?a'));
        $myPlaceholders->addPlaceholder(Helper::getMockCustomPlaceholder('?b'));

        $transformer = new QueryTransformer($myAdapter, $myPlaceholders);

        $expected = <<<EXPECTED
{
            (?>
                # Ignored chunks.
                (?>
                    # Comment.
                    -- [^\\r\\n]*
                )
                  |
                (?>
                    # DB-specifics.
                    " (?> [^"\\\\]+|\\\\"|\\\\)* "
                )
            )
              |
            (?>
                # Optional blocks
                \{
                    # Use "+" here, not "*"! Else nested blocks are not processed well.
                    ( (?> (?>[^{}]+)  |  (?R) )* )             #1
                \}
            )
              |
            (?>
                # Placeholder
                (\?) ( [ab]? )                           #2 #3
            )
        }sx
EXPECTED;

        $this->assertEquals($expected, $transformer->getRegexpMain());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithExpandValue()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);
        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT "?m", ?m, ?m SQL_TEXT', ['in1', 'in2']),
            true
        );

        $this->assertEquals('SQL_TEXT "?m", "out1", "out2" SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals([], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValue()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);
        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT "?m", ?m, ?m SQL_TEXT', ['in1', 'in2']),
            false
        );

        $this->assertEquals('SQL_TEXT "?m", ?, ? SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals(['out1', 'out2'], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValueRecursiveCallbackOne()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);
        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT {?m, {?m, {?m, {?m}}}} SQL_TEXT', ['in1', 'in2', 'in3', 'in4']),
            false
        );

        $this->assertEquals('SQL_TEXT ?, ?, ?, ? SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals(['out1', 'out2', 'out3', 'out4'], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValueRecursiveCallbackTwo()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);
        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT {?m, {?m, {?m, {?m}}}} SQL_TEXT', ['in1', 'in2', Database::SKIP_VALUE, 'in4']),
            false
        );

        $this->assertEquals('SQL_TEXT ?, ?,  SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals(['out1', 'out2'], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValueRecursiveCallbackThree()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);
        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT ?m,{?m, ?m} ?m SQL_TEXT', ['in1', 'in2', Database::SKIP_VALUE, 'in3']),
            false
        );

        $this->assertEquals('SQL_TEXT ?, ? SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals(['out1', 'out3'], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithExpandValueRecursiveCallbackFour()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);

        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT {?m, ?m} SQL_TEXT', ['in1', true]),
            true
        );
        $this->assertEquals('SQL_TEXT "out1", TRUE SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals([], $result->getValues());

        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT {?m, ?m} SQL_TEXT', ['in1', false]),
            true
        );
        $this->assertEquals('SQL_TEXT "out1", FALSE SQL_TEXT', $result->getQueryAsText());
        $this->assertEquals([], $result->getValues());
    }


    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValueEmptyValues()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);

        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT ?m, ?m, ?m, ?m, ?m SQL_TEXT', ['', false, true, 0, 'skip_me_please']),
            false
        );
        $this->assertEquals('SQL_TEXT ?, ?, ?, ?, SQL_PART_WITHOUT_PARAMS SQL_TEXT', $result->getQueryAsText());
        $this->assertSame(['', false, true, 0], $result->getValues());
    }

    public function testTransformQuery_CustomAdapterCustomPlaceholderWithCommonNativePlaceholderWithoutExpandValueFewValues()
    {
        $myAdapter = Helper::getMockCustomAdapter();
        $placeholders = new PlaceholderCollection();
        $placeholders->addPlaceholder(Helper::getMockCustomPlaceholder('?m'));

        $transformer = new QueryTransformer($myAdapter, $placeholders);

        $result = $transformer->transformQuery(
            Query::create('SQL_TEXT ?m, ?m SQL_TEXT', ['in1']),
            false
        );
        $this->assertEquals('SQL_TEXT ?, ERROR_NO_VALUE SQL_TEXT', $result->getQueryAsText());
        $this->assertSame(['out1'], $result->getValues());
    }

}
