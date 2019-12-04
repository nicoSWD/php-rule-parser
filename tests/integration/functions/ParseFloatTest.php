<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration\functions;

use nicoSWD\Rule\tests\integration\AbstractTestBase;

final class ParseFloatTest extends AbstractTestBase
{
    /** @test */
    public function onStringLiteral()
    {
        $this->assertTrue($this->evaluate('parseFloat("3.1337") === 3.1337'));
    }

    /** @test */
    public function onStringLiteralWithSpaces()
    {
        $this->assertTrue($this->evaluate('parseFloat(" 3.1 ") === 3.1'));
    }

    /** @test */
    public function onStringLiteralWithNonNumericChars()
    {
        $this->assertTrue($this->evaluate('parseFloat("3.12aaa") === 3.12'));
    }

    /** @test */
    public function onUserDefinedVariable()
    {
        $this->assertTrue($this->evaluate('parseFloat(foo) === 3.4', ['foo' => '3.4']));
        $this->assertFalse($this->evaluate('parseFloat(foo) === "3.5"', ['foo' => 3.5]));
    }

    /** @test */
    public function callWithoutArgsShouldReturnNaN()
    {
        $this->assertFalse($this->evaluate('parseFloat() === 1'));
    }
}
