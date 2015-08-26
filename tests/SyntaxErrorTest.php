<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * Class SyntaxErrorTest
 */
class SyntaxErrorTest extends \AbstractTestBase
{
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected token "(" at position 19 on line 1
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

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing operator
     */
    public function testMissingOperatorThrowsException()
    {
        $rule = 'TOTALAMOUNT == -1 TOTALAMOUNT > 10';

        $this->evaluate($rule, [
            'TOTALAMOUNT' => '-1'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing opening parenthesis at position 6
     */
    public function testMissingOpeningParenthesisThrowsException()
    {
        $this->evaluate('1 == 1)', []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Missing closing parenthesis
     */
    public function testMissingClosingParenthesisThrowsException()
    {
        $this->evaluate('(1 == 1', []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown token "-" at position 10
     */
    public function testMisplacedMinusThrowsException()
    {
        $this->evaluate('1 == 1 && -foo == 1', ['foo' => 1]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Undefined variable "foo" at position 12 on line 2
     */
    public function testUndefinedVariableThrowsException()
    {
        $rule = ' // new line on purpose
            foo == "MA"';

        $this->evaluate($rule, []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Incomplete expression
     */
    public function testIncompleteExpressionExceptionIsThrownCorrectly()
    {
        $rule = '1 == 1 && COUNTRY';

        $this->evaluate($rule, [
            'COUNTRY' => 'MA'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Undefined variable "COUNTRY" at position 0
     */
    public function testRulesEvaluatesTrueThrowsExceptionsForUndefinedVars()
    {
        $rule = 'COUNTRY=="MA"';

        $this->evaluate($rule, []);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Incomplete and/or condition
     */
    public function testRulesEvaluatesTrueThrowsExceptionsOnSyntaxErrors()
    {
        $rule = 'COUNTRY == "MA" &&';

        $this->evaluate($rule, [
            'COUNTRY' => 'EG'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unexpected "&&" at position 19 on line 1
     */
    public function testMultipleLogicalTokensThrowException()
    {
        $rule = 'COUNTRY == "MA" && &&';

        $this->evaluate($rule, [
            'COUNTRY' => 'EG'
        ]);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown token "^" at position 16
     */
    public function testUnknownTokenExceptionIsThrown()
    {
        $rule = 'COUNTRY == "MA" ^';

        $this->evaluate($rule, [
            'COUNTRY' => 'MA'
        ]);
    }
}
