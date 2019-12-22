<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Token;

use InvalidArgumentException;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\Token\TokenEqualStrict;
use PHPUnit\Framework\TestCase;

final class TokenFactoryTest extends TestCase
{
    /** @var Token\TokenFactory */
    private $tokenFactory;

    protected function setUp(): void
    {
        $this->tokenFactory = new Token\TokenFactory();
    }

    public function testSimpleTypeReturnsCorrectInstance()
    {
        $this->assertInstanceOf(Token\TokenNull::class, $this->tokenFactory->createFromPHPType(null));
        $this->assertInstanceOf(Token\TokenString::class, $this->tokenFactory->createFromPHPType('string sample'));
        $this->assertInstanceOf(Token\TokenFloat::class, $this->tokenFactory->createFromPHPType(0.3));
        $this->assertInstanceOf(Token\TokenInteger::class, $this->tokenFactory->createFromPHPType(4));
        $this->assertInstanceOf(Token\TokenBool::class, $this->tokenFactory->createFromPHPType(true));
        $this->assertInstanceOf(Token\TokenArray::class, $this->tokenFactory->createFromPHPType([1, 2]));
    }

    public function testUnsupportedTypeThrowsException()
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage('Unsupported PHP type: "resource"');

        $this->tokenFactory->createFromPHPType(tmpfile());
    }

    public function testGivenAnInvalidTokenNameItShouldThrowAnException()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->tokenFactory->createFromTokenName('betrunken');
    }

    public function testGivenAValidTokenNameItShouldReturnItsCorrespondingClassName()
    {
        $this->assertSame(TokenEqualStrict::class, $this->tokenFactory->createFromTokenName(Token\Token::EQUAL_STRICT));
    }
}
