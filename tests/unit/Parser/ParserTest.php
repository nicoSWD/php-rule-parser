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
use nicoSWD\Rule\AST\BoolNode;
use nicoSWD\Rule\AST\ComparisonNode;
use nicoSWD\Rule\AST\ComparisonOperator;
use nicoSWD\Rule\AST\IntegerNode;
use nicoSWD\Rule\AST\LogicalNode;
use nicoSWD\Rule\AST\LogicalOperator;
use nicoSWD\Rule\AST\StringNode;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\TokenIterator;
use nicoSWD\Rule\TokenStream\TokenStream;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ParserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private TokenStream|m\Mock $tokenStream;
    private Parser $parser;

    protected function setUp(): void
    {
        $this->tokenStream = m::mock(TokenStream::class);
        $this->parser = new Parser($this->tokenStream);
    }

    #[Test]
    public function givenARuleStringWhenValidItShouldReturnTheCompiledRule(): void
    {
        $tokens = [
            new Token\TokenOpeningParenthesis('('),
            new Token\TokenInteger(1),
            new Token\TokenEqual('=='),
            new Token\TokenString('1'),
            new Token\TokenClosingParenthesis(')'),
            new Token\TokenAnd('&&'),
            new Token\TokenInteger(2),
            new Token\TokenGreater('>'),
            new Token\TokenInteger(1),
            new Token\TokenSpace(' '),
            new Token\TokenComment('// true dat!')
        ];

        $arrayIterator = new ArrayIterator($tokens);
        $tokenIterator = new TokenIterator($arrayIterator, $this->tokenStream);

        $this->tokenStream->shouldReceive('getStream')->once()->andReturn($tokenIterator);

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
