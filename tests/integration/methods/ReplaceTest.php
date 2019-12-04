<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ReplaceTest extends AbstractTestBase
{
    /** @test */
    public function validNeedleReturnsCorrectPosition()
    {
        $this->assertTrue($this->evaluate('foo.replace("a", "A") === "bAr"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".replace("r", "R") === "baR"'));
    }

    /** @test */
    public function omittedParametersDoNotReplaceAnything()
    {
        $this->assertTrue($this->evaluate('"bar".replace() === "bar"'));
    }

    /** @test */
    public function omittedSecondParameterReplacesWithUndefined()
    {
        $this->assertTrue($this->evaluate('"bar".replace("r") === "baundefined"'));
    }

    /** @test */
    public function replaceWithRegularExpression()
    {
        $this->assertTrue($this->evaluate('"arbar".replace(/ar$/, "") === "arb"'));
    }

    /** @test */
    public function regularExpressionWithGModifier()
    {
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/, "") === "foo"'));
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/g, "") === ""'));
    }
}
