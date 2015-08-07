<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\arrays;

/**
 * Class ArraysTest
 */
class ArraysTest extends \AbstractTestBase
{
    public function testArraysEqualUserSuppliedArrays()
    {
        $this->assertTrue($this->evaluate(
            'foo === ["foo", "bar", 1, true]',
            ['foo' => ['foo', 'bar', 1, true]]
        ));

        $this->assertTrue($this->evaluate('[123, 12] === foo && bar === [23]', [
            'foo' => [123, 12],
            'bar' => [23]
        ]));
    }

    public function testEmptyArrayDoesParseCorrectly()
    {
        $this->assertTrue($this->evaluate('[] === []'));
    }

    public function testLiteralArrayComparison()
    {
        $this->assertTrue($this->evaluate('[123, 12] === [123, 12]'));
        $this->assertFalse($this->evaluate('[123, 12] === [123, 12, 1]'));
    }

    public function testCommentsAreIgnoredInArray()
    {
        $this->assertTrue($this->evaluate(
            'foo === [
                "foo", // This is foo
                "bar"  // And this is bar
            ]',
            ['foo' => ['foo', 'bar']]
        ));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "," at position 15 on line 1
     */
    public function testTrailingCommaThrowsException()
    {
        $this->evaluate('["foo", "bar", ] === ["foo", "bar"]');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "," at position 15 on line 1
     */
    public function testLineIsReportedCorrectlyOnSyntaxError2()
    {
        $this->evaluate('["foo", "bar", ,] === ["foo", "bar"]');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "," at position 12 on line 5
     */
    public function testLineIsReportedCorrectlyOnSyntaxError()
    {
        $this->evaluate(
            '[
                "foo",
                "bar",
                // Missing value after comma
            ] === ["foo", "bar"]'
        );
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected value at position 8 on line 1
     */
    public function testMissingCommaThrowsException()
    {
        $this->evaluate('["foo"  "bar"] === ["foo", "bar"]');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "===" at position 8 on line 1
     */
    public function testUnexpectedTokenThrowsException()
    {
        $this->evaluate('["foo", ===] === ["foo", "bar"]');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected end of string. Expected "]"
     */
    public function testUnexpectedEndOfStringThrowsException()
    {
        $this->evaluate('["foo", "bar"');
    }
}
