<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BaseTokenTest extends TestCase
{
    /** @var BaseToken */
    private $token;

    protected function setUp(): void
    {
        $this->token = new class('&&', 1337) extends BaseToken {
            public function getKind(): TokenKind
            {
                return TokenKind::AND;
            }
        };
    }

    #[Test]
    public function givenATokenWhenGettingOffsetItShouldReturnTheExpectedOffset(): void
    {
        $this->assertSame(1337, $this->token->getOffset());
    }

    #[Test]
    public function givenATokenWhenGettingValueItShouldReturnTheExpectedValue(): void
    {
        $this->assertSame('&&', $this->token->getValue());
    }

    #[Test]
    public function givenATokenWhenGettingOriginalValueItShouldReturnTheExpectedOriginalValue(): void
    {
        $this->assertSame('&&', $this->token->getOriginalValue());
    }

    #[Test]
    public function givenALogicalTokenWhenCheckingTypeItShouldReturnTrueForLogicalAndFalseForComma(): void
    {
        $this->assertTrue($this->token->isOfKind(TokenKind::AND));
        $this->assertFalse($this->token->isOfKind(TokenKind::COMMA));
    }
}
