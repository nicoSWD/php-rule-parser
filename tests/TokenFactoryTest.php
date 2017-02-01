<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\tests;

use nicoSWD\Rules\Tokens\TokenFactory;

class TokenFactoryTest extends \AbstractTestBase
{
    public function testSimpleTypeReturnsCorrectInstance()
    {
        $this->assertInstanceOf('nicoSWD\Rules\Tokens\TokenNull', TokenFactory::createFromPHPType(null));
        $this->assertInstanceOf('nicoSWD\Rules\Tokens\TokenString', TokenFactory::createFromPHPType('string sample'));
        $this->assertInstanceOf('nicoSWD\Rules\Tokens\TokenFloat', TokenFactory::createFromPHPType(0.3));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Unsupported PHP type: "object"
     */
    public function testUnsupportedTypeThrowsException()
    {
        TokenFactory::createFromPHPType(new \StdClass());
    }
}
