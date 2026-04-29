<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class ReplaceTest extends AbstractTestBase
{
    #[Test]
    public function validNeedleReturnsCorrectPosition(): void
    {
        $this->assertTrue($this->evaluate('foo.replace("a", "A") === "bAr"', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".replace("r", "R") === "baR"'));
    }

    #[Test]
    public function omittedParametersDoNotReplaceAnything(): void
    {
        $this->assertTrue($this->evaluate('"bar".replace() === "bar"'));
    }

    #[Test]
    public function omittedSecondParameterReplacesWithUndefined(): void
    {
        $this->assertTrue($this->evaluate('"bar".replace("r") === "baundefined"'));
    }

    #[Test]
    public function replaceWithRegularExpression(): void
    {
        $this->assertTrue($this->evaluate('"arbar".replace(/ar$/, "") === "arb"'));
    }

    #[Test]
    public function regularExpressionWithGModifier(): void
    {
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/, "") === "foo"'));
        $this->assertTrue($this->evaluate('"foofoo".replace(/foo/g, "") === ""'));
    }
}
