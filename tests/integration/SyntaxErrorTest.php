<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests;

use nicoSWD\Rules\Rule;
use nicoSWD\Rules\tests\integration\AbstractTestBase;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class SyntaxErrorTest extends AbstractTestBase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "(" at position 19 on line 1
     */
    public function testEmptyParenthesisThrowException()
    {
        $rule = '(totalamount != 3) ()';

        $this->evaluate($rule, [
            'totalamount' => '-1'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "==" at position 11 on line 1
     */
    public function testDoubleOperatorThrowsException()
    {
        $rule = 'country == == "EMD"';

        $this->evaluate($rule, [
            'country' => 'GLF',
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Incomplete expression for token "==" at position 0 on line 1
     */
    public function testMissingLeftValueThrowsException()
    {
        $rule = '== "EMD"';

        $this->evaluate($rule, [
            'country' => 'GLF',
        ]);
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
        $this->assertSame('Missing opening parenthesis at position 6 on line 1', $rule->getError());
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
        $this->assertSame('Unknown token "-" at position 10 on line 1', $rule->getError());
    }

    public function testUndefinedVariableThrowsException()
    {
        $rule = new Rule(' // new line on purpose
            foo == "MA"', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "foo" at position 12 on line 2', $rule->getError());
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
        $this->assertSame('Undefined variable "nonono" at position 0 on line 1', $rule->getError());
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
        $this->assertSame('Unexpected "&&" at position 19 on line 1', $rule->getError());
    }

    public function testUnknownTokenExceptionIsThrown()
    {
        $rule = new Rule('country == "MA" ^', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token "^" at position 16 on line 1', $rule->getError());
    }
}
