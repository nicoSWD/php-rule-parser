<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\methods;

use nicoSWD\Rule\Rule;
use nicoSWD\Rule\tests\integration\AbstractTestBase;

class ToUpperCaseTest extends AbstractTestBase
{
    public function testSpacesBetweenVariableAndMethodWork()
    {
        $this->assertTrue($this->evaluate('foo . toUpperCase() === "BAR"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate(
            'foo
                .
            toUpperCase() === "BAR"',
            ['foo' => 'bar']
        ));
    }

    public function testIfCallOnStringLiteralsWorks()
    {
        $this->assertTrue($this->evaluate('"bar".toUpperCase() === "BAR"'));
        $this->assertTrue($this->evaluate('"bar" . toUpperCase() === "BAR"'));
    }

    public function testIfMethodCanBeCalledOnVariablesHoldingIntegers()
    {
        $this->assertTrue($this->evaluate('foo.toUpperCase() === "1"', ['foo' => 1]));
    }

    public function testCallOnIntegersThrowsException()
    {
        $rule = new Rule('1.toUpperCase() === "1"', ['foo' => 1]);

        $this->assertFalse($rule->isValid());
        $this->assertSame('Unknown token ".toUpperCase(" at position 1', $rule->getError());
    }
}
