<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class TokenFactoryTest extends TestCase
{
    private readonly TokenFactory $tokenFactory;

    protected function setUp(): void
    {
        $this->tokenFactory = new TokenFactory();
    }

    #[Test]
    public function simpleTypeReturnsCorrectInstance(): void
    {
        $this->assertSame(TokenKind::NULL, $this->tokenFactory->createFromPHPType(null)->getKind());
        $this->assertSame(TokenKind::STRING, $this->tokenFactory->createFromPHPType('string sample')->getKind());
        $this->assertSame(TokenKind::FLOAT, $this->tokenFactory->createFromPHPType(0.3)->getKind());
        $this->assertSame(TokenKind::INTEGER, $this->tokenFactory->createFromPHPType(4)->getKind());
        $this->assertSame(TokenKind::BOOL_TRUE, $this->tokenFactory->createFromPHPType(true)->getKind());
        $this->assertSame(TokenKind::ARRAY, $this->tokenFactory->createFromPHPType([1, 2])->getKind());
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
        $token = $this->tokenFactory->createFromToken(TokenKind::EQUAL_STRICT, ['EqualStrict' => '==='], 0);
        $this->assertSame(TokenKind::EQUAL_STRICT, $token->getKind());
    }
}
