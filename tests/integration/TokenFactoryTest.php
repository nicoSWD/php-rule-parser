<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\tests;

use nicoSWD\Rules\tests\integration\AbstractTestBase;
use nicoSWD\Rules\Tokens\TokenFactory;
use nicoSWD\Rules\Tokens\TokenFloat;
use nicoSWD\Rules\Tokens\TokenNull;
use nicoSWD\Rules\Tokens\TokenString;

class TokenFactoryTest extends AbstractTestBase
{
    public function testSimpleTypeReturnsCorrectInstance()
    {
        $tokenFactory = new TokenFactory();

        $this->assertInstanceOf(TokenNull::class, $tokenFactory->createFromPHPType(null));
        $this->assertInstanceOf(TokenString::class, $tokenFactory->createFromPHPType('string sample'));
        $this->assertInstanceOf(TokenFloat::class, $tokenFactory->createFromPHPType(0.3));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unsupported PHP type: "object"
     */
    public function testUnsupportedTypeThrowsException()
    {
        $tokenFactory = new TokenFactory();
        $tokenFactory->createFromPHPType(new \stdClass());
    }
}
