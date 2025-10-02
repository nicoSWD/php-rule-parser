<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream\Token;

use Mockery\MockInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenType;
use nicoSWD\Rule\TokenStream\TokenIterator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class BaseTokenTest extends TestCase
{
    /** @var BaseToken */
    private $token;

    protected function setUp(): void
    {
        $this->token = new class('&&', 1337) extends BaseToken {
            public function getType(): TokenType
            {
                return TokenType::LOGICAL;
            }
        };
    }

    #[Test]
    public function offset(): void
    {
        $this->assertSame(1337, $this->token->getOffset());
    }

    #[Test]
    public function getValue(): void
    {
        $this->assertSame('&&', $this->token->getValue());
    }

    #[Test]
    public function getOriginalValue(): void
    {
        $this->assertSame('&&', $this->token->getOriginalValue());
    }

    #[Test]
    public function createNode(): void
    {
        /** @var TokenIterator|MockInterface $tokenStream */
        $tokenStream = \Mockery::mock(TokenIterator::class);
        $this->assertSame($this->token, $this->token->createNode($tokenStream));
    }

    #[Test]
    public function isOfType(): void
    {
        $this->assertTrue($this->token->isOfType(TokenType::LOGICAL));
        $this->assertFalse($this->token->isOfType(TokenType::COMMA));
    }
}
