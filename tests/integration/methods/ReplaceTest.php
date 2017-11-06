<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

class ReplaceTest extends AbstractTestBase
{
    public function testValidNeedleReturnsCorrectPosition()
    {
        $this->assertTrue($this->evaluate('foo.replace("a", "A") === "bAr"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".replace("r", "R") === "baR"'));
    }

    public function testOmittedParametersDoNotReplaceAnything()
    {
        $this->assertTrue($this->evaluate('"bar".replace() === "bar"'));
    }

    public function testOmittedSecondParameterReplacesWithUndefined()
    {
        $this->assertTrue($this->evaluate('"bar".replace("r") === "baundefined"'));
    }

    public function testReplaceWithRegularExpression()
    {
        $this->assertTrue($this->evaluate('"arbar".replace(/ar$/, "") === "arb"'));
    }

    public function testRegularExpressionWithGModifier()
    {
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/, "") === "foo"'));
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/g, "") === ""'));
    }
}
