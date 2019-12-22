<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream;

use Mockery\MockInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenType;
use nicoSWD\Rule\TokenStream\TokenStream;
use PHPUnit\Framework\TestCase;

final class BaseTokenTest extends TestCase
{
    /** @var BaseToken */
    private $token;

    protected function setUp(): void
    {
        $this->token = new class('&&', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::LOGICAL;
            }
        };
    }

    /** @test */
    public function offset(): void
    {
        $this->assertSame(1337, $this->token->getOffset());
    }

    /** @test */
    public function getValue(): void
    {
        $this->assertSame('&&', $this->token->getValue());
    }

    /** @test */
    public function getOriginalValue(): void
    {
        $this->assertSame('&&', $this->token->getOriginalValue());
    }

    /** @test */
    public function createNode(): void
    {
        /** @var TokenStream|MockInterface $tokenStream */
        $tokenStream = \Mockery::mock(TokenStream::class);
        $this->assertSame($this->token, $this->token->createNode($tokenStream));
    }

    /** @test */
    public function isOfType(): void
    {
        $this->assertTrue($this->token->isOfType(TokenType::LOGICAL));
        $this->assertFalse($this->token->isOfType(TokenType::COMMA));
    }

    /** @test */
    public function isValue(): void
    {
        $token = new class('123', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::VALUE;
            }
        };

        $this->assertTrue($token->isValue());
    }

    /** @test */
    public function isWhitespace(): void
    {
        $token = new class(' ', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::SPACE;
            }
        };

        $this->assertTrue($token->isWhitespace());
    }

    /** @test */
    public function isMethod(): void
    {
        $token = new class('.derp(', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::METHOD;
            }
        };

        $this->assertTrue($token->isMethod());
    }

    /** @test */
    public function isComma(): void
    {
        $token = new class(',', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::COMMA;
            }
        };

        $this->assertTrue($token->isComma());
    }

    /** @test */
    public function isOperator(): void
    {
        $token = new class('>', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::OPERATOR;
            }
        };

        $this->assertTrue($token->isOperator());
    }

    /** @test */
    public function isLogical(): void
    {
        $token = new class('&&', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::LOGICAL;
            }
        };

        $this->assertTrue($token->isLogical());
    }

    /** @test */
    public function isParenthesis(): void
    {
        $token = new class('(', 1337) extends BaseToken {
            public function getType(): int
            {
                return TokenType::PARENTHESIS;
            }
        };

        $this->assertTrue($token->isParenthesis());
    }
}
