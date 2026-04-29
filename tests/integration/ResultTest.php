<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\RuleEngine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    #[Test]
    public function arithmeticResultReturnsComputedValue(): void
    {
        $rule = new Rule('5 * 3');
        $this->assertSame(15, $rule->result());
    }

    #[Test]
    public function additionResultReturnsSum(): void
    {
        $rule = new Rule('10 + 20');
        $this->assertSame(30, $rule->result());
    }

    #[Test]
    public function subtractionResultReturnsDifference(): void
    {
        $rule = new Rule('100 - 25');
        $this->assertSame(75, $rule->result());
    }

    #[Test]
    public function divisionResultReturnsQuotient(): void
    {
        $rule = new Rule('100 / 4');
        $this->assertSame(25, $rule->result());
    }

    #[Test]
    public function moduloResultReturnsRemainder(): void
    {
        $rule = new Rule('10 % 3');
        $this->assertSame(1, $rule->result());
    }

    #[Test]
    public function complexArithmeticResult(): void
    {
        $rule = new Rule('2 + 3 * 4 - 6 / 2');
        $this->assertSame(11, $rule->result());
    }

    #[Test]
    public function arithmeticWithParentheses(): void
    {
        $rule = new Rule('(2 + 3) * 4');
        $this->assertSame(20, $rule->result());
    }

    #[Test]
    public function stringConcatenationResult(): void
    {
        $rule = new Rule('"hello " + "world"');
        $this->assertSame('hello world', $rule->result());
    }

    #[Test]
    public function stringConcatenationWithVariables(): void
    {
        $rule = new Rule('foo + "bar"', ['foo' => 'foo']);
        $this->assertSame('foobar', $rule->result());
    }

    #[Test]
    public function comparisonResultReturnsBool(): void
    {
        $rule = new Rule('5 > 3');
        $this->assertTrue($rule->result());

        $rule = new Rule('5 < 3');
        $this->assertFalse($rule->result());
    }

    #[Test]
    public function logicalResultReturnsBool(): void
    {
        $rule = new Rule('true && false');
        $this->assertFalse($rule->result());

        $rule = new Rule('true || false');
        $this->assertTrue($rule->result());
    }

    #[Test]
    public function functionCallResult(): void
    {
        $rule = new Rule('parseInt("42")');
        $this->assertSame(42, $rule->result());
    }

    #[Test]
    public function methodCallResult(): void
    {
        $rule = new Rule('"FOO".toLowerCase()');
        $this->assertSame('foo', $rule->result());
    }

    #[Test]
    public function methodCallWithArgumentsResult(): void
    {
        $rule = new Rule('"hello world".indexOf("world")');
        $this->assertSame(6, $rule->result());
    }

    #[Test]
    public function variableResult(): void
    {
        $rule = new Rule('foo', ['foo' => 42]);
        $this->assertSame(42, $rule->result());
    }

    #[Test]
    public function nullResult(): void
    {
        $rule = new Rule('null');
        $this->assertNull($rule->result());
    }

    #[Test]
    public function booleanResult(): void
    {
        $rule = new Rule('true');
        $this->assertTrue($rule->result());

        $rule = new Rule('false');
        $this->assertFalse($rule->result());
    }

    #[Test]
    public function floatResult(): void
    {
        $rule = new Rule('3.14');
        $this->assertSame(3.14, $rule->result());
    }

    #[Test]
    public function integerResult(): void
    {
        $rule = new Rule('42');
        $this->assertSame(42, $rule->result());
    }

    #[Test]
    public function stringResult(): void
    {
        $rule = new Rule('"hello"');
        $this->assertSame('hello', $rule->result());
    }

    #[Test]
    public function arrayResult(): void
    {
        $rule = new Rule('[1, 2, 3]');
        $this->assertSame([1, 2, 3], $rule->result());
    }

    #[Test]
    public function methodReturningArrayResult(): void
    {
        $rule = new Rule('"foo,bar,baz".split(",")');
        $this->assertSame(['foo', 'bar', 'baz'], $rule->result());
    }

    #[Test]
    public function resultWithVariablesInArithmetic(): void
    {
        $rule = new Rule('price * quantity', ['price' => 100, 'quantity' => 3]);
        $this->assertSame(300, $rule->result());
    }

    #[Test]
    public function resultWithRuleEngine(): void
    {
        $engine = new RuleEngine(defaultVariables: ['x' => 10]);
        $this->assertSame(50, $engine->result('x * 5'));
    }

    #[Test]
    public function isTrueStillWorksAfterResult(): void
    {
        $rule = new Rule('5 * 3 > 10');
        $this->assertTrue($rule->result());
        $this->assertTrue($rule->isTrue());
    }

    #[Test]
    public function resultStillWorksAfterIsTrue(): void
    {
        $rule = new Rule('5 * 3');
        $this->assertTrue($rule->isTrue());
        $this->assertSame(15, $rule->result());
    }

    #[Test]
    public function resultWithComparisonAndArithmetic(): void
    {
        $rule = new Rule('price * quantity > 250', ['price' => 100, 'quantity' => 3]);
        $this->assertTrue($rule->result());
    }

    #[Test]
    public function resultWithRegexTest(): void
    {
        $rule = new Rule('/^he/.test("hello")');
        $this->assertTrue($rule->result());
    }

    #[Test]
    public function resultWithInOperator(): void
    {
        $rule = new Rule('3 in [1, 2, 3, 4]');
        $this->assertTrue($rule->result());
    }
}
