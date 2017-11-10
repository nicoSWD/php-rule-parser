<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\arrays;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

class ArraysTest extends AbstractTestBase
{
    public function testArraysEqualUserSuppliedArrays()
    {
        $this->assertTrue($this->evaluate(
            'foo === ["foo1", "bar", 2, true]',
            ['foo' => ['foo1', 'bar', 2, true]]
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
        // $this->assertTrue($this->evaluate('[123, [12, 1]] === [123, [12, 1]]'));
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

    public function testTrailingCommaThrowsException()
    {
        $rule = new Rule('["foo", "bar", ] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 15', $rule->getError());
    }

    public function testLineIsReportedCorrectlyOnSyntaxError2()
    {
        $rule = new Rule('["foo", "bar", ,] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 15', $rule->getError());
    }

    public function testMissingCommaThrowsException()
    {
        $rule = new Rule('["foo"  "bar"] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "bar" at position 8', $rule->getError());
    }

    public function testUnexpectedTokenThrowsException()
    {
        $rule = new Rule('["foo", ===] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "===" at position 8', $rule->getError());
    }

    public function testUnexpectedEndOfStringThrowsException()
    {
        $rule = new Rule('["foo", "bar"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected end of string', $rule->getError());
    }
}
