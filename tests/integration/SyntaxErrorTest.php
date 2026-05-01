<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\Rule;
use PHPUnit\Framework\Attributes\Test;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
final class SyntaxErrorTest extends AbstractTestBase
{
    #[Test]
    public function emptyParenthesisThrowException(): void
    {
        $rule = new Rule('(totalamount != 3) ()', [
            'totalamount' => '-1',
        ]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "(" at position 19', $rule->error);
    }

    #[Test]
    public function doubleOperatorThrowsException(): void
    {
        $rule = new Rule('country == == "venezuela"', ['country' => 'spain']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "==" at position 11', $rule->error);
    }

    #[Test]
    public function missingLeftValueThrowsException(): void
    {
        $rule = new Rule('== "venezuela"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "==" at position 0', $rule->error);
    }

    #[Test]
    public function missingOperatorThrowsException(): void
    {
        $rule = new Rule('total == -1 total > 10', ['total' => 12]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "total" at position 12', $rule->error);
    }

    #[Test]
    public function missingOpeningParenthesisThrowsException(): void
    {
        $rule = new Rule('1 == 1)');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected ")" at position 6', $rule->error);
    }

    #[Test]
    public function missingClosingParenthesisThrowsException(): void
    {
        $rule = new Rule('(1 == 1');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected end of string', $rule->error);
    }

    #[Test]
    public function unaryMinusOnVariableIsValid(): void
    {
        $rule = new Rule('1 == 1 && -foo == 1', ['foo' => 1]);

        $this->assertTrue($rule->isValid());
        $this->assertSame('', $rule->error);
    }

    #[Test]
    public function undefinedVariableThrowsException(): void
    {
        $rule = new Rule(' // new line on purpose
            foo == "MA"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "foo" at position 36', $rule->error);
    }

    #[Test]
    public function incompleteExpressionExceptionIsThrownCorrectly(): void
    {
        $rule = new Rule('1 == 1 && country', ['country' => 'es']);

        $this->assertTrue($rule->isValid());
        $this->assertTrue($rule->isTrue());
    }

    #[Test]
    public function rulesEvaluatesTrueThrowsExceptionsForUndefinedVars(): void
    {
        $rule = new Rule('nonono=="MA"');

        $this->assertFalse($rule->isValid());
        $this->assertSame('Undefined variable "nonono" at position 0', $rule->error);
    }

    #[Test]
    public function rulesEvaluatesTrueThrowsExceptionsOnSyntaxErrors(): void
    {
        $rule = new Rule('country == "MA" &&', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected end of string', $rule->error);
    }

    #[Test]
    public function multipleLogicalTokensThrowException(): void
    {
        $rule = new Rule('country == "MA" && &&', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "&&" at position 19', $rule->error);
    }

    #[Test]
    public function unknownTokenExceptionIsThrown(): void
    {
        $rule = new Rule('country == "MA" ^', ['country' => 'es']);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unexpected "^" at position 16', $rule->error);
    }
}
