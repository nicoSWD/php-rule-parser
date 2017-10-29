<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests\functions;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class ParseIntTest extends AbstractTestBase
{
    public function testOnStringLiteral()
    {
        $this->assertTrue($this->evaluate('parseInt("3") === 3'));
    }

    public function testOnStringLiteralWithSpaces()
    {
        $this->assertTrue($this->evaluate('parseInt(" 3 ") === 3'));
    }

    public function testOnStringLiteralWithNonNumericChars()
    {
        $this->assertTrue($this->evaluate('parseInt("3aaa") === 3'));
    }

    public function testOnUserDefinedVariable()
    {
        $this->assertTrue($this->evaluate('parseInt(foo) === 3', ['foo' => '3']));
        $this->assertFalse($this->evaluate('parseInt(foo) === "3"', ['foo' => 3]));
    }

    public function testCallWithoutArgsShouldReturnNan()
    {
        $this->assertFalse($this->evaluate('parseInt() === 1'));
    }
}
