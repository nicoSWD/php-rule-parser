<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\tests\integration\functions;

use nicoSWD\Rule\tests\integration\AbstractTestBase;
use PHPUnit\Framework\Attributes\Test;

final class ParseFloatTest extends AbstractTestBase
{
    #[Test]
    public function onStringLiteral(): void
    {
        $this->assertTrue($this->evaluate('parseFloat("3.1337") === 3.1337'));
    }

    #[Test]
    public function onStringLiteralWithSpaces(): void
    {
        $this->assertTrue($this->evaluate('parseFloat(" 3.1 ") === 3.1'));
    }

    #[Test]
    public function onStringLiteralWithNonNumericChars(): void
    {
        $this->assertTrue($this->evaluate('parseFloat("3.12aaa") === 3.12'));
    }

    #[Test]
    public function onUserDefinedVariable(): void
    {
        $this->assertTrue($this->evaluate('parseFloat(foo) === 3.4', ['foo' => '3.4']));
        $this->assertFalse($this->evaluate('parseFloat(foo) === "3.5"', ['foo' => 3.5]));
    }

    #[Test]
    public function callWithoutArgsShouldReturnNaN(): void
    {
        $this->assertFalse($this->evaluate('parseFloat() === 1'));
    }
}
