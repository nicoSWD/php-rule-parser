<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class SyntaxErrorTest extends AbstractTestBase
{
    public function testEmptyParenthesisThrowException()
    {
        $rule = new Rule('(totalamount != 3) ()', [
            'totalamount' => '-1'
        ]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "(" at position 19', $rule->getError());
    }

    public function testDoubleOperatorThrowsException()
    {
        $rule = new Rule('country == == "venezuela"', ['country' => 'spain']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "==" at position 11', $rule->getError());
    }

    public function testMissingLeftValueThrowsException()
    {
        $rule = new Rule('== "venezuela"', [
            'country' => 'spain',
        ]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Incomplete expression for token "=="', $rule->getError());
    }

    public function testMissingOperatorThrowsException()
    {
        $rule = new Rule('total == -1 total > 10', ['total' => 12]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Missing operator', $rule->getError());
    }

    public function testMissingOpeningParenthesisThrowsException()
    {
        $rule = new Rule('1 == 1)');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Missing opening parenthesis', $rule->getError());
    }

    public function testMissingClosingParenthesisThrowsException()
    {
        $rule = new Rule('(1 == 1');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Missing closing parenthesis', $rule->getError());
    }

    public function testMisplacedMinusThrowsException()
    {
        $rule = new Rule('1 == 1 && -foo == 1', ['foo' => 1]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token "-" at position 10', $rule->getError());
    }

    public function testUndefinedVariableThrowsException()
    {
        $rule = new Rule(' // new line on purpose
            foo == "MA"', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "foo" at position 36', $rule->getError());
    }

    public function testIncompleteExpressionExceptionIsThrownCorrectly()
    {
        $rule = new Rule('1 == 1 && country', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Incomplete condition', $rule->getError());
    }

    public function testRulesEvaluatesTrueThrowsExceptionsForUndefinedVars()
    {
        $rule = new Rule('nonono=="MA"', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "nonono" at position 0', $rule->getError());
    }

    public function testRulesEvaluatesTrueThrowsExceptionsOnSyntaxErrors()
    {
        $rule = new Rule('country == "MA" &&', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Incomplete condition', $rule->getError());
    }

    public function testMultipleLogicalTokensThrowException()
    {
        $rule = new Rule('country == "MA" && &&', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "&&" at position 19', $rule->getError());
    }

    public function testUnknownTokenExceptionIsThrown()
    {
        $rule = new Rule('country == "MA" ^', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token "^" at position 16', $rule->getError());
    }
}
