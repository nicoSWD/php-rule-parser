<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class IndexOfTest extends AbstractTestBase
{
    /** @test */
    public function validNeedleReturnsCorrectPosition(): void
    {
        $this->assertTrue($this->evaluate('foo.indexOf("a") === 1', ['foo' => 'bar']));
        $this->assertTrue($this->evaluate('"bar".indexOf("b") === 0'));
    }

    /** @test */
    public function omittedParameterReturnsNegativeOne(): void
    {
        $this->assertTrue($this->evaluate('"bar".indexOf() === -1'));
    }

    /** @test */
    public function negativeOneIsReturnedIfNeedleNotFound(): void
    {
        $this->assertTrue($this->evaluate('"bar".indexOf("foo") === -1'));
    }
}
