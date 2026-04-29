<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class ToUpperCaseTest extends AbstractTestBase
{
    #[Test]
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

    #[Test]
    public function ifCallOnStringLiteralsWorks(): void
    {
        $this->assertTrue($this->evaluate('"bar".toUpperCase() === "BAR"'));
        $this->assertTrue($this->evaluate('"bar" . toUpperCase() === "BAR"'));
    }

    #[Test]
    public function ifMethodCanBeCalledOnVariablesHoldingIntegers(): void
    {
        $this->assertTrue($this->evaluate('foo.toUpperCase() === "1"', ['foo' => 1]));
    }

    #[Test]
    public function callOnIntegersThrowsException(): void
    {
        $rule = new Rule('1.toUpperCase() === "1"', ['foo' => 1]);

        $this->assertTrue($rule->isValid());
        $this->assertTrue($rule->isTrue());
    }
}
