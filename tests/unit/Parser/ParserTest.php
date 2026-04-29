<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\Parser;

use ArrayIterator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use nicoSWD\Rule\AST\ComparisonNode;
use nicoSWD\Rule\AST\ComparisonOperator;
use nicoSWD\Rule\AST\IntegerNode;
use nicoSWD\Rule\AST\LogicalNode;
use nicoSWD\Rule\AST\LogicalOperator;
use nicoSWD\Rule\AST\StringNode;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\TokenStream\FunctionRegistry;
use nicoSWD\Rule\TokenStream\MethodRegistry;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use nicoSWD\Rule\TokenStream\TokenIteratorFactory;
use nicoSWD\Rule\TokenStream\VariableRegistry;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ParserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private TokenIteratorFactory $tokenIteratorFactory;
    private TokenizerInterface|m\Mock $tokenizer;
    private Parser $parser;

    protected function setUp(): void
    {
        $variableRegistry = m::mock(VariableRegistry::class);
        $functionRegistry = m::mock(FunctionRegistry::class);
        $methodRegistry = m::mock(MethodRegistry::class);
        $this->tokenIteratorFactory = new TokenIteratorFactory(
            $variableRegistry,
            $functionRegistry,
            $methodRegistry,
        );
        $this->tokenizer = m::mock(TokenizerInterface::class);
        $this->parser = new Parser($this->tokenIteratorFactory, $this->tokenizer);
    }

    #[Test]
    public function givenARuleStringWhenValidItShouldReturnTheCompiledRule(): void
    {
        $tokens = [
            new GenericToken(TokenKind::OPENING_PARENTHESIS, '('),
            new Token\TokenInteger(1),
            new GenericToken(TokenKind::EQUAL, '=='),
            new Token\TokenString('1'),
            new GenericToken(TokenKind::CLOSING_PARENTHESIS, ')'),
            new GenericToken(TokenKind::AND, '&&'),
            new Token\TokenInteger(2),
            new GenericToken(TokenKind::GREATER, '>'),
            new Token\TokenInteger(1),
            new GenericToken(TokenKind::SPACE, ' '),
            new GenericToken(TokenKind::COMMENT, '// true dat!')
        ];

        $arrayIterator = new ArrayIterator($tokens);

        $this->tokenizer->shouldReceive('tokenize')->once()->andReturn($arrayIterator);

        $ast = $this->parser->parse('(1=="1")&&2>1 // true dat!');

        // Verify the AST structure
        $this->assertInstanceOf(LogicalNode::class, $ast);
        $this->assertSame(LogicalOperator::AND, $ast->operator);

        // Left side: (1=="1") → ComparisonNode
        $this->assertInstanceOf(ComparisonNode::class, $ast->left);
        $this->assertInstanceOf(IntegerNode::class, $ast->left->left);
        $this->assertSame(1, $ast->left->left->value);
        $this->assertInstanceOf(StringNode::class, $ast->left->right);
        $this->assertSame('1', $ast->left->right->value);
        $this->assertSame(ComparisonOperator::EQUAL, $ast->left->operator);

        // Right side: 2>1 → ComparisonNode
        $this->assertInstanceOf(ComparisonNode::class, $ast->right);
        $this->assertInstanceOf(IntegerNode::class, $ast->right->left);
        $this->assertSame(2, $ast->right->left->value);
        $this->assertInstanceOf(IntegerNode::class, $ast->right->right);
        $this->assertSame(1, $ast->right->right->value);
        $this->assertSame(ComparisonOperator::GREATER_THAN, $ast->right->operator);
    }
}
