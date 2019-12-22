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
    public function validNeedleReturnsCorrectPosition(): void
    {
        $this->assertTrue($this->evaluate('foo.replace("a", "A") === "bAr"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".replace("r", "R") === "baR"'));
    }

    /** @test */
    public function omittedParametersDoNotReplaceAnything(): void
    {
        $this->assertTrue($this->evaluate('"bar".replace() === "bar"'));
    }

    /** @test */
    public function omittedSecondParameterReplacesWithUndefined(): void
    {
        $this->assertTrue($this->evaluate('"bar".replace("r") === "baundefined"'));
    }

    /** @test */
    public function replaceWithRegularExpression(): void
    {
        $this->assertTrue($this->evaluate('"arbar".replace(/ar$/, "") === "arb"'));
    }

    /** @test */
    public function regularExpressionWithGModifier(): void
    {
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/, "") === "foo"'));
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/g, "") === ""'));
    }
}
