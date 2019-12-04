<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Rule;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
final class SyntaxErrorTest extends AbstractTestBase
{
    /** @test */
    public function emptyParenthesisThrowException()
    {
        $rule = new Rule('(totalamount != 3) ()', [
            'totalamount' => '-1'
        ]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "(" at position 19', $rule->getError());
    }

    /** @test */
    public function doubleOperatorThrowsException()
    {
        $rule = new Rule('country == == "venezuela"', ['country' => 'spain']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "==" at position 11', $rule->getError());
    }

    /** @test */
    public function missingLeftValueThrowsException()
    {
        $rule = new Rule('== "venezuela"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Incomplete expression for token "=="', $rule->getError());
    }

    /** @test */
    public function missingOperatorThrowsException()
    {
        $rule = new Rule('total == -1 total > 10', ['total' => 12]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Missing operator', $rule->getError());
    }

    /** @test */
    public function missingOpeningParenthesisThrowsException()
    {
        $rule = new Rule('1 == 1)');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Missing opening parenthesis', $rule->getError());
    }

    /** @test */
    public function missingClosingParenthesisThrowsException()
    {
        $rule = new Rule('(1 == 1');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Missing closing parenthesis', $rule->getError());
    }

    /** @test */
    public function misplacedMinusThrowsException()
    {
        $rule = new Rule('1 == 1 && -foo == 1', ['foo' => 1]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token "-" at position 10', $rule->getError());
    }

    /** @test */
    public function undefinedVariableThrowsException()
    {
        $rule = new Rule(' // new line on purpose
            foo == "MA"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "foo" at position 36', $rule->getError());
    }

    /** @test */
    public function incompleteExpressionExceptionIsThrownCorrectly()
    {
        $rule = new Rule('1 == 1 && country', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Incomplete condition', $rule->getError());
    }

    /** @test */
    public function rulesEvaluatesTrueThrowsExceptionsForUndefinedVars()
    {
        $rule = new Rule('nonono=="MA"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "nonono" at position 0', $rule->getError());
    }

    /** @test */
    public function rulesEvaluatesTrueThrowsExceptionsOnSyntaxErrors()
    {
        $rule = new Rule('country == "MA" &&', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Incomplete condition', $rule->getError());
    }

    /** @test */
    public function multipleLogicalTokensThrowException()
    {
        $rule = new Rule('country == "MA" && &&', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "&&" at position 19', $rule->getError());
    }

    /** @test */
    public function unknownTokenExceptionIsThrown()
    {
        $rule = new Rule('country == "MA" ^', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token "^" at position 16', $rule->getError());
    }
}
