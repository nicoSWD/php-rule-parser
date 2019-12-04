<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\functions;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ParseIntTest extends AbstractTestBase
{
    /** @test */
    public function onStringLiteral()
    {
        $this->assertTrue($this->evaluate('parseInt("3") === 3'));
    }

    /** @test */
    public function onStringLiteralWithSpaces()
    {
        $this->assertTrue($this->evaluate('parseInt(" 3 ") === 3'));
    }

    /** @test */
    public function onStringLiteralWithNonNumericChars()
    {
        $this->assertTrue($this->evaluate('parseInt("3aaa") === 3'));
    }

    /** @test */
    public function onUserDefinedVariable()
    {
        $this->assertTrue($this->evaluate('parseInt(foo) === 3', ['foo' => '3']));
        $this->assertFalse($this->evaluate('parseInt(foo) === "3"', ['foo' => 3]));
    }

    /** @test */
    public function callWithoutArgsShouldReturnNan()
    {
        $this->assertFalse($this->evaluate('parseInt() === 1'));
    }
}
