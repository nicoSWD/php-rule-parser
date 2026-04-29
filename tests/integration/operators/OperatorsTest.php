<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\integration\operators;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class OperatorsTest extends AbstractTestBase
{
    #[Test]
    public function allAvailableOperators(): void
    {
        $this->assertTrue($this->evaluate('3 == 3'), 'Equal operator failed on two integers');
        $this->assertTrue($this->evaluate('4 === 4'));
        $this->assertTrue($this->evaluate('"4" == 4'));
        $this->assertTrue($this->evaluate('2 > 1'));
        $this->assertTrue($this->evaluate('1 < 2'));
        $this->assertTrue($this->evaluate('1 <> 2'));
        $this->assertTrue($this->evaluate('1 != 2'));
        $this->assertTrue($this->evaluate('1 <= 2'));
        $this->assertTrue($this->evaluate('2 <= 2'));
        $this->assertTrue($this->evaluate('3 >= 2'));
        $this->assertTrue($this->evaluate('2 >= 2'));

        $this->assertFalse($this->evaluate('2 !== 2'));
    }

    #[Test]
    public function strictOperators(): void
    {
        $this->assertFalse($this->evaluate('"4" === 4'));
        $this->assertTrue($this->evaluate('4 === 4'));

        $this->assertTrue($this->evaluate('4 !== "4"'));
        $this->assertFalse($this->evaluate('4 !== 4'));
    }

    #[Test]
    public function inOperator(): void
    {
        $this->assertTrue($this->evaluate('123 in foo', ['foo' => [123, 12]]));
        $this->assertFalse($this->evaluate('"123" in foo', ['foo' => [123, 12]]));
        $this->assertFalse($this->evaluate('"123" in [123, 12]'));
        $this->assertTrue($this->evaluate('123 in [123, 12]'));
    }

    #[Test]
    public function inOperatorOnReturnedValueByMethodCall(): void
    {
        $this->assertTrue($this->evaluate('"123" in "321,123".split(",")'));
    }

    #[Test]
    public function inOperatorWithNonArrayRightValueThrowsException(): void
    {
        $rule = new Rule('"123" in "foo"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Expected array, got "string"', $rule->error);
    }

    #[Test]
    public function commentsAreIgnoredCorrectly(): void
    {
        $this->assertFalse($this->evaluate('1 == 2 // || 1 == 1'));
        $this->assertTrue($this->evaluate('1 == 1 // && 2 == 1'));
        $this->assertFalse($this->evaluate('1 == 1 /* || 2 == 1 */ && 2 != 2'));
        $this->assertTrue($this->evaluate('1 == 3 /* || 2 == 1 */ || 2 == 2'));
        $this->assertTrue($this->evaluate(
            '1 /* test */ == 1 /* test */ && /* test */ 2 /* test */ == /* test */ 2'
        ));
    }

    #[Test]
    public function equalOperator(): void
    {
        $this->assertTrue($this->evaluate('foo == -1', ['foo' => -1]));
        $this->assertFalse($this->evaluate('foo == 3', ['foo' => -1]));
        $this->assertTrue($this->evaluate('foo != 3 && 3 != foo', ['foo' => -1]));
        $this->assertFalse($this->evaluate('foo != 3 && 3 != foo', ['foo' => 3]));
        $this->assertTrue($this->evaluate('foo != 3 && 3 != foo', ['foo' => -3]));
    }

    #[Test]
    public function stringConcatenationWithPlus(): void
    {
        $this->assertTrue($this->evaluate('"foo" + "bar" == "foobar"'));
        $this->assertTrue($this->evaluate('"hello " + "world" == "hello world"'));
        $this->assertTrue($this->evaluate('"foo" + "bar" + "baz" == "foobarbaz"'));
    }

    #[Test]
    public function stringConcatenationWithVariables(): void
    {
        $this->assertTrue($this->evaluate('foo + "bar" == "foobar"', ['foo' => 'foo']));
        $this->assertTrue($this->evaluate('"foo" + bar == "foobar"', ['bar' => 'bar']));
        $this->assertTrue($this->evaluate('foo + bar == "foobar"', ['foo' => 'foo', 'bar' => 'bar']));
    }

    #[Test]
    public function stringConcatenationInComparison(): void
    {
        $this->assertTrue($this->evaluate('"hello " + "world" == "hello world"'));
        $this->assertFalse($this->evaluate('"hello " + "world" == "goodbye"'));
    }

    #[Test]
    public function stringConcatenationWithMethodCalls(): void
    {
        $this->assertTrue($this->evaluate('"foo".toUpperCase() + "bar" == "FOObar"'));
        $this->assertTrue($this->evaluate('"foo" + "bar".toUpperCase() == "fooBAR"'));
    }

    #[Test]
    public function numericAdditionWithPlus(): void
    {
        $this->assertTrue($this->evaluate('1 + 2 == 3'));
        $this->assertTrue($this->evaluate('10 + 20 == 30'));
        $this->assertTrue($this->evaluate('1 + 2 + 3 == 6'));
    }

    #[Test]
    public function numericAdditionWithVariables(): void
    {
        $this->assertTrue($this->evaluate('foo + 2 == 5', ['foo' => 3]));
        $this->assertTrue($this->evaluate('foo + bar == 7', ['foo' => 3, 'bar' => 4]));
    }

    #[Test]
    public function numericAdditionInComparison(): void
    {
        $this->assertTrue($this->evaluate('1 + 2 > 2'));
        $this->assertTrue($this->evaluate('1 + 2 < 4'));
        $this->assertTrue($this->evaluate('1 + 2 == 3'));
    }

    #[Test]
    public function concatenationWithLogicalOperators(): void
    {
        $this->assertTrue($this->evaluate('"foo" + "bar" == "foobar" && 1 + 2 == 3'));
        $this->assertTrue($this->evaluate('"foo" + "bar" == "foobar" || 1 + 2 == 99'));
    }
}
