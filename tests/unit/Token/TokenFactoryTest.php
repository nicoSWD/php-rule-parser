<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\Token\TokenEqualStrict;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class TokenFactoryTest extends TestCase
{
    private readonly Token\TokenFactory $tokenFactory;

    protected function setUp(): void
    {
        $this->tokenFactory = new Token\TokenFactory();
    }

    #[Test]
    public function simpleTypeReturnsCorrectInstance(): void
    {
        $this->assertInstanceOf(Token\TokenNull::class, $this->tokenFactory->createFromPHPType(null));
        $this->assertInstanceOf(Token\TokenString::class, $this->tokenFactory->createFromPHPType('string sample'));
        $this->assertInstanceOf(Token\TokenFloat::class, $this->tokenFactory->createFromPHPType(0.3));
        $this->assertInstanceOf(Token\TokenInteger::class, $this->tokenFactory->createFromPHPType(4));
        $this->assertInstanceOf(Token\TokenBoolTrue::class, $this->tokenFactory->createFromPHPType(true));
        $this->assertInstanceOf(Token\TokenArray::class, $this->tokenFactory->createFromPHPType([1, 2]));
    }

    #[Test]
    public function unsupportedTypeThrowsException(): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('Unsupported PHP type: "resource"');

        $this->tokenFactory->createFromPHPType(tmpfile());
    }

    #[Test]
    public function givenAValidTokenNameItShouldReturnItsCorrespondingClassName(): void
    {
        $this->assertSame(TokenEqualStrict::class, $this->tokenFactory->createFromToken(Token\Token::EQUAL_STRICT));
    }
}
