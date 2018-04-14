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

class BaseTokenTest extends TestCase
{
    /** @var BaseToken */
    private $token;

    protected function setUp()
    {
        $this->token = new class ('&&', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::LOGICAL;
            }
        };
    }

    public function testOffset()
    {
        $this->assertSame(1337, $this->token->getOffset());
    }

    public function testGetValue()
    {
        $this->assertSame('&&', $this->token->getValue());
    }

    public function testGetOriginalValue()
    {
        $this->assertSame('&&', $this->token->getOriginalValue());
    }

    public function testCreateNode()
    {
        /** @var TokenStream|MockInterface $tokenStream */
        $tokenStream = \Mockery::mock(TokenStream::class);
        $this->assertSame($this->token, $this->token->createNode($tokenStream));
    }

    public function testIsOfType()
    {
        $this->assertTrue($this->token->isOfType(TokenType::LOGICAL));
        $this->assertFalse($this->token->isOfType(TokenType::COMMA));
    }

    public function testIsValue()
    {
        $token = new class ('123', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::VALUE;
            }
        };

        $this->assertTrue($token->isValue());
    }

    public function testIsWhitespace()
    {
        $token = new class (' ', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::SPACE;
            }
        };

        $this->assertTrue($token->isWhitespace());
    }

    public function testIsMethod()
    {
        $token = new class ('.derp(', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::METHOD;
            }
        };

        $this->assertTrue($token->isMethod());
    }

    public function testIsComma()
    {
        $token = new class (',', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::COMMA;
            }
        };

        $this->assertTrue($token->isComma());
    }

    public function testIsOperator()
    {
        $token = new class ('>', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::OPERATOR;
            }
        };

        $this->assertTrue($token->isOperator());
    }

    public function testIsLogical()
    {
        $token = new class ('&&', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::LOGICAL;
            }
        };

        $this->assertTrue($token->isLogical());
    }

    public function testIsParenthesis()
    {
        $token = new class ('(', 1337) extends BaseToken
        {
            public function getType(): int
            {
                return TokenType::PARENTHESIS;
            }
        };

        $this->assertTrue($token->isParenthesis());
    }
}
