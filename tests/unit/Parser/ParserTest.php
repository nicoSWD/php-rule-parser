<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Parser;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use nicoSWD\Rule\TokenStream\AST;
use nicoSWD\Rule\Compiler\CompilerFactoryInterface;
use nicoSWD\Rule\Compiler\CompilerInterface;
use nicoSWD\Rule\Expression\BaseExpression;
use nicoSWD\Rule\Expression\ExpressionFactoryInterface;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\TokenStream;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private AST|m\Mock $ast;
    private ExpressionFactoryInterface|m\Mock $expressionFactory;
    private CompilerFactoryInterface|m\Mock $compilerFactory;
    private Parser $parser;

    protected function setUp(): void
    {
        $this->ast = m::mock(AST::class);
        $this->expressionFactory = m::mock(ExpressionFactoryInterface::class);
        $this->compilerFactory = m::mock(CompilerFactoryInterface::class);

        $this->parser = new Parser($this->ast, $this->expressionFactory, $this->compilerFactory);
    }

    /** @test */
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

        $compiler = m::mock(CompilerInterface::class);
        $compiler->shouldReceive('addLogical')->once();
        $compiler->shouldReceive('addParentheses')->twice();
        $compiler->shouldReceive('addBoolean')->twice();
        $compiler->shouldReceive('getCompiledRule')->once()->andReturn('(1)&1');

        /** @var m\MockInterface $tokenStream */
        $tokenStream = \Mockery::mock(TokenStream::class);
        $tokenStream->shouldReceive('rewind')->once();
        $tokenStream->shouldReceive('next');
        $tokenStream->shouldReceive('current')->andReturn(...$tokens);
        $tokenStream->shouldReceive('valid')->andReturnUsing(function () use (&$tokens) {
            return !!next($tokens);
        });

        $this->compilerFactory->shouldReceive('create')->once()->andReturn($compiler);
        $this->ast->shouldReceive('getStream')->once()->andReturn($tokenStream);

        $equalExpression = m::mock(BaseExpression::class);
        $equalExpression->shouldReceive('evaluate')->once()->with(1, '1');

        $greaterExpression = m::mock(BaseExpression::class);
        $greaterExpression->shouldReceive('evaluate')->once()->with(2, 1);

        $this->expressionFactory
            ->shouldReceive('createFromOperator')
            ->twice()
            ->with(m::type(Token\BaseToken::class))
            ->andReturn(
                $equalExpression,
                $greaterExpression
            );

        $this->assertSame('(1)&1', $this->parser->parse('(1=="1")&&2>1 // true dat!'));
    }
}
