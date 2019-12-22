<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\arrays;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ArraysTest extends AbstractTestBase
{
    /** @test */
    public function arraysEqualUserSuppliedArrays(): void
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

    /** @test */
    public function emptyArrayDoesParseCorrectly(): void
    {
        $this->assertTrue($this->evaluate('[] === []'));
    }

    /** @test */
    public function literalArrayComparison(): void
    {
        $this->assertTrue($this->evaluate('[123, 12] === [123, 12]'));
        $this->assertFalse($this->evaluate('[123, 12] === [123, 12, 1]'));
    }

    /** @test */
    public function commentsAreIgnoredInArray(): void
    {
        $this->assertTrue($this->evaluate(
            'foo === [
                "foo", // This is foo
                "bar"  // And this is bar
            ]',
            ['foo' => ['foo', 'bar']]
        ));
    }

    /** @test */
    public function trailingCommaThrowsException(): void
    {
        $rule = new Rule('["foo", "bar", ] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 15', $rule->getError());
    }

    /** @test */
    public function lineIsReportedCorrectlyOnSyntaxError2(): void
    {
        $rule = new Rule('["foo", "bar", ,] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "," at position 15', $rule->getError());
    }

    /** @test */
    public function missingCommaThrowsException(): void
    {
        $rule = new Rule('["foo"  "bar"] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "bar" at position 8', $rule->getError());
    }

    /** @test */
    public function unexpectedTokenThrowsException(): void
    {
        $rule = new Rule('["foo", ===] === ["foo", "bar"]');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "===" at position 8', $rule->getError());
    }

    /** @test */
    public function unexpectedEndOfStringThrowsException(): void
    {
        $rule = new Rule('["foo", "bar"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected end of string', $rule->getError());
    }
}
