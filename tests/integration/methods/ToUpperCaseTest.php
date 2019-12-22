<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ToUpperCaseTest extends AbstractTestBase
{
    /** @test */
    public function spacesBetweenVariableAndMethodWork(): void
    {
        $this->assertTrue($this->evaluate('foo . toUpperCase() === "BAR"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate(
            'foo
                .
            toUpperCase() === "BAR"',
            ['foo' => 'bar']
        ));
    }

    /** @test */
    public function ifCallOnStringLiteralsWorks(): void
    {
        $this->assertTrue($this->evaluate('"bar".toUpperCase() === "BAR"'));
        $this->assertTrue($this->evaluate('"bar" . toUpperCase() === "BAR"'));
    }

    /** @test */
    public function ifMethodCanBeCalledOnVariablesHoldingIntegers(): void
    {
        $this->assertTrue($this->evaluate('foo.toUpperCase() === "1"', ['foo' => 1]));
    }

    /** @test */
    public function callOnIntegersThrowsException(): void
    {
        $rule = new Rule('1.toUpperCase() === "1"', ['foo' => 1]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token ".toUpperCase(" at position 1', $rule->getError());
    }
}
