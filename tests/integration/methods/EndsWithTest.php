<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\methods;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class EndsWithTest extends AbstractTestBase
{
    /** @test */
    public function givenAStringWhenEndsWithNeedleItShouldReturnTrue(): void
    {
        $this->assertTrue($this->evaluate('foo.endsWith("llo") === true', ['foo' => 'hello']));
        $this->assertTrue($this->evaluate('"hello".endsWith("llo") === true'));
    }

    /** @test */
    public function givenAStringWhenNotEndsWithNeedleItShouldReturnFalse(): void
    {
        $this->assertTrue($this->evaluate('"hello".endsWith("ell") === false'));
    }
}
