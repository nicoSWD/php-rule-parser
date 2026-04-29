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

    // --- Arithmetic operator tests ---

    #[Test]
    public function subtraction(): void
    {
        $this->assertTrue($this->evaluate('5 - 3 == 2'));
        $this->assertTrue($this->evaluate('10 - 7 == 3'));
        $this->assertTrue($this->evaluate('100 - 50 - 25 == 25'));
    }

    #[Test]
    public function subtractionWithVariables(): void
    {
        $this->assertTrue($this->evaluate('foo - 2 == 3', ['foo' => 5]));
        $this->assertTrue($this->evaluate('foo - bar == 1', ['foo' => 5, 'bar' => 4]));
    }

    #[Test]
    public function subtractionInComparison(): void
    {
        $this->assertTrue($this->evaluate('5 - 3 > 1'));
        $this->assertTrue($this->evaluate('5 - 3 < 3'));
        $this->assertTrue($this->evaluate('5 - 3 == 2'));
    }

    #[Test]
    public function multiplication(): void
    {
        $this->assertTrue($this->evaluate('3 * 4 == 12'));
        $this->assertTrue($this->evaluate('10 * 10 == 100'));
        $this->assertTrue($this->evaluate('2 * 3 * 4 == 24'));
    }

    #[Test]
    public function multiplicationWithVariables(): void
    {
        $this->assertTrue($this->evaluate('foo * 3 == 15', ['foo' => 5]));
        $this->assertTrue($this->evaluate('foo * bar == 20', ['foo' => 4, 'bar' => 5]));
    }

    #[Test]
    public function multiplicationInComparison(): void
    {
        $this->assertTrue($this->evaluate('3 * 4 > 10'));
        $this->assertTrue($this->evaluate('3 * 4 < 15'));
        $this->assertTrue($this->evaluate('3 * 4 == 12'));
    }

    #[Test]
    public function division(): void
    {
        $this->assertTrue($this->evaluate('10 / 2 == 5'));
        $this->assertTrue($this->evaluate('100 / 4 == 25'));
        $this->assertTrue($this->evaluate('100 / 5 / 2 == 10'));
    }

    #[Test]
    public function divisionWithVariables(): void
    {
        $this->assertTrue($this->evaluate('foo / 2 == 5', ['foo' => 10]));
        $this->assertTrue($this->evaluate('foo / bar == 3', ['foo' => 15, 'bar' => 5]));
    }

    #[Test]
    public function divisionInComparison(): void
    {
        $this->assertTrue($this->evaluate('10 / 2 > 4'));
        $this->assertTrue($this->evaluate('10 / 2 < 6'));
        $this->assertTrue($this->evaluate('10 / 2 == 5'));
    }

    #[Test]
    public function modulo(): void
    {
        $this->assertTrue($this->evaluate('10 % 3 == 1'));
        $this->assertTrue($this->evaluate('100 % 50 == 0'));
        $this->assertTrue($this->evaluate('7 % 2 == 1'));
    }

    #[Test]
    public function moduloWithVariables(): void
    {
        $this->assertTrue($this->evaluate('foo % 3 == 1', ['foo' => 10]));
        $this->assertTrue($this->evaluate('foo % bar == 0', ['foo' => 20, 'bar' => 5]));
    }

    #[Test]
    public function moduloInComparison(): void
    {
        $this->assertTrue($this->evaluate('10 % 3 == 1'));
        $this->assertTrue($this->evaluate('10 % 3 < 2'));
        $this->assertTrue($this->evaluate('10 % 3 > 0'));
    }

    #[Test]
    public function operatorPrecedenceMultiplicationBeforeAddition(): void
    {
        // 2 + 3 * 4 should be 2 + (3 * 4) = 14, not (2 + 3) * 4 = 20
        $this->assertTrue($this->evaluate('2 + 3 * 4 == 14'));
        $this->assertFalse($this->evaluate('2 + 3 * 4 == 20'));
    }

    #[Test]
    public function operatorPrecedenceDivisionBeforeSubtraction(): void
    {
        // 10 - 6 / 2 should be 10 - (6 / 2) = 7, not (10 - 6) / 2 = 2
        $this->assertTrue($this->evaluate('10 - 6 / 2 == 7'));
        $this->assertFalse($this->evaluate('10 - 6 / 2 == 2'));
    }

    #[Test]
    public function operatorPrecedenceMixed(): void
    {
        // 2 + 3 * 4 - 6 / 2 = 2 + 12 - 3 = 11
        $this->assertTrue($this->evaluate('2 + 3 * 4 - 6 / 2 == 11'));
    }

    #[Test]
    public function operatorPrecedenceWithParentheses(): void
    {
        // Parentheses override precedence
        $this->assertTrue($this->evaluate('(2 + 3) * 4 == 20'));
        $this->assertTrue($this->evaluate('(10 - 6) / 2 == 2'));
        $this->assertTrue($this->evaluate('(2 + 3) * (4 - 1) == 15'));
    }

    #[Test]
    public function arithmeticWithMethodCalls(): void
    {
        $this->assertTrue($this->evaluate('"foo".indexOf("f") + 2 == 2'));
        $this->assertTrue($this->evaluate('2 * "foo".indexOf("o") == 2'));
    }

    #[Test]
    public function arithmeticWithLogicalOperators(): void
    {
        $this->assertTrue($this->evaluate('1 + 2 == 3 && 3 * 4 == 12'));
        $this->assertTrue($this->evaluate('10 / 2 == 5 || 1 + 1 == 99'));
    }

    #[Test]
    public function negativeNumbersStillWork(): void
    {
        $this->assertTrue($this->evaluate('-1 == -1'));
        $this->assertTrue($this->evaluate('foo == -1', ['foo' => -1]));
        $this->assertTrue($this->evaluate('-5 + 3 == -2'));
        $this->assertTrue($this->evaluate('-5 - 3 == -8'));
    }

    #[Test]
    public function subtractionVsNegativeNumber(): void
    {
        // 5 - 3 should be subtraction, not 5 and -3
        $this->assertTrue($this->evaluate('5 - 3 == 2'));
        // -3 alone should be a negative number
        $this->assertTrue($this->evaluate('-3 == -3'));
    }

    // --- Unary operator tests ---

    #[Test]
    public function unaryMinusNegatesNumber(): void
    {
        $this->assertTrue($this->evaluate('-5 == -5'));
        $this->assertTrue($this->evaluate('--5 == 5'));
        $this->assertTrue($this->evaluate('---5 == -5'));
    }

    #[Test]
    public function unaryMinusWithExpression(): void
    {
        $this->assertTrue($this->evaluate('-(5 + 3) == -8'));
        $this->assertTrue($this->evaluate('-(5 - 3) == -2'));
        $this->assertTrue($this->evaluate('-foo == -5', ['foo' => 5]));
    }

    #[Test]
    public function unaryMinusWithVariable(): void
    {
        $this->assertTrue($this->evaluate('-foo == -10', ['foo' => 10]));
        $this->assertTrue($this->evaluate('--foo == 10', ['foo' => 10]));
    }

    #[Test]
    public function unaryMinusPrecedence(): void
    {
        // -5 * 3 should be (-5) * 3 = -15
        $this->assertTrue($this->evaluate('-5 * 3 == -15'));
        // -(5 * 3) = -15
        $this->assertTrue($this->evaluate('-(5 * 3) == -15'));
        // -5 + 3 should be (-5) + 3 = -2
        $this->assertTrue($this->evaluate('-5 + 3 == -2'));
    }

    #[Test]
    public function logicalNotWithBool(): void
    {
        $this->assertTrue($this->evaluate('!false'));
        $this->assertFalse($this->evaluate('!true'));
        $this->assertTrue($this->evaluate('!!true'));
        $this->assertFalse($this->evaluate('!!false'));
    }

    #[Test]
    public function logicalNotWithExpression(): void
    {
        $this->assertTrue($this->evaluate('!(1 == 2)'));
        $this->assertFalse($this->evaluate('!(1 == 1)'));
        $this->assertTrue($this->evaluate('!(1 == 2 && 2 == 2)'));
    }

    #[Test]
    public function logicalNotWithVariable(): void
    {
        $this->assertTrue($this->evaluate('!foo', ['foo' => false]));
        $this->assertFalse($this->evaluate('!foo', ['foo' => true]));
        $this->assertTrue($this->evaluate('!!foo', ['foo' => true]));
    }

    #[Test]
    public function logicalNotWithComparison(): void
    {
        $this->assertTrue($this->evaluate('!(1 > 2)'));
        $this->assertFalse($this->evaluate('!(1 < 2)'));
        $this->assertTrue($this->evaluate('!(1 == 2)'));
    }

    #[Test]
    public function logicalNotWithMethodCall(): void
    {
        $this->assertFalse($this->evaluate('!"foo".indexOf("x") == -1'));
        $this->assertTrue($this->evaluate('!"foo".indexOf("f") == -1'));
    }

    #[Test]
    public function combinedUnaryOperators(): void
    {
        $this->assertTrue($this->evaluate('!(-5 == -5) == false'));
        $this->assertTrue($this->evaluate('-!true == -0'));
        $this->assertTrue($this->evaluate('!(-1 == 1)'));
    }

    #[Test]
    public function divisionByZeroReturnsInfinity(): void
    {
        $this->assertTrue($this->evaluate('1 / 0 == foo', ['foo' => INF]));
        $this->assertTrue($this->evaluate('-1 / 0 == foo', ['foo' => -INF]));
    }

    #[Test]
    public function zeroDividedByZeroReturnsNan(): void
    {
        $this->assertFalse($this->evaluate('0 / 0 == 0'));
        $this->assertFalse($this->evaluate('0 / 0 == foo', ['foo' => 0]));
    }

    #[Test]
    public function moduloByZeroReturnsNan(): void
    {
        $this->assertFalse($this->evaluate('5 % 0 == 0'));
        $this->assertFalse($this->evaluate('5 % 0 == foo', ['foo' => 0]));
    }

    #[Test]
    public function normalDivisionStillWorks(): void
    {
        $this->assertTrue($this->evaluate('10 / 2 == 5'));
        $this->assertTrue($this->evaluate('100 / 4 == 25'));
        $this->assertTrue($this->evaluate('100 / 5 / 2 == 10'));
    }

    #[Test]
    public function normalModuloStillWorks(): void
    {
        $this->assertTrue($this->evaluate('10 % 3 == 1'));
        $this->assertTrue($this->evaluate('100 % 50 == 0'));
        $this->assertTrue($this->evaluate('7 % 2 == 1'));
    }
}
