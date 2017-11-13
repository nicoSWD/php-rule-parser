<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Token;

use nicoSWD\Rule\TokenStream\Token;
use PHPUnit\Framework\TestCase;

class TokenFactoryTest extends TestCase
{
    public function testSimpleTypeReturnsCorrectInstance()
    {
        $tokenFactory = new Token\TokenFactory();

        $this->assertInstanceOf(Token\TokenNull::class, $tokenFactory->createFromPHPType(null));
        $this->assertInstanceOf(Token\TokenString::class, $tokenFactory->createFromPHPType('string sample'));
        $this->assertInstanceOf(Token\TokenFloat::class, $tokenFactory->createFromPHPType(0.3));
        $this->assertInstanceOf(Token\TokenInteger::class, $tokenFactory->createFromPHPType(4));
        $this->assertInstanceOf(Token\TokenBool::class, $tokenFactory->createFromPHPType(true));
        $this->assertInstanceOf(Token\TokenArray::class, $tokenFactory->createFromPHPType([1, 2]));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unsupported PHP type: "object"
     */
    public function testUnsupportedTypeThrowsException()
    {
        $tokenFactory = new Token\TokenFactory();
        $tokenFactory->createFromPHPType(new \stdClass());
    }
}
