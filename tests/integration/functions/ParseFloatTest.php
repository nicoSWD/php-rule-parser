<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\tests\functions;

use nicoSWD\Rules\tests\integration\AbstractTestBase;

class ParseFloatTest extends AbstractTestBase
{
    public function testOnStringLiteral()
    {
        $this->assertTrue($this->evaluate('parseFloat("3.1337") === 3.1337'));
    }

    public function testOnStringLiteralWithSpaces()
    {
        $this->assertTrue($this->evaluate('parseFloat(" 3.1 ") === 3.1'));
    }

    public function testOnStringLiteralWithNonNumericChars()
    {
        $this->assertTrue($this->evaluate('parseFloat("3.12aaa") === 3.12'));
    }

    public function testOnUserDefinedVariable()
    {
        $this->assertTrue($this->evaluate('parseFloat(foo) === 3.4', ['foo' => '3.4']));
        $this->assertFalse($this->evaluate('parseFloat(foo) === "3.5"', ['foo' => 3.5]));
    }

    public function testCallWithoutArgsShouldReturnNaN()
    {
        $this->assertFalse($this->evaluate('parseFloat() === 1'));
    }
}
