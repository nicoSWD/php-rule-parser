<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\tests\unit;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use nicoSWD\Rules\Compiler\CompilerFactoryInterface;
use nicoSWD\Rules\Compiler\CompilerInterface;
use nicoSWD\Rules\Expressions\BaseExpression;
use nicoSWD\Rules\Expressions\ExpressionFactoryInterface;
use nicoSWD\Rules\Parser\Parser;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenAnd;
use nicoSWD\Rules\Tokens\TokenClosingParenthesis;
use nicoSWD\Rules\Tokens\TokenComment;
use nicoSWD\Rules\Tokens\TokenEqual;
use nicoSWD\Rules\Tokens\TokenGreater;
use nicoSWD\Rules\Tokens\TokenInteger;
use nicoSWD\Rules\Tokens\TokenOpeningParenthesis;
use nicoSWD\Rules\Tokens\TokenSpace;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\TokenStream\AST;
use nicoSWD\Rules\TokenStream\TokenStream;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var AST|m\Mock */
    private $ast;
    /** @var ExpressionFactoryInterface|m\Mock */
    private $expressionFactory;
    /** @var CompilerFactoryInterface|m\Mock */
    private $compilerFactory;
    /** @var Parser */
    private $parser;

    protected function setUp()
    {
        $this->ast = m::mock(AST::class);
        $this->expressionFactory = m::mock(ExpressionFactoryInterface::class);
        $this->compilerFactory = m::mock(CompilerFactoryInterface::class);

        $this->parser = new Parser($this->ast, $this->expressionFactory, $this->compilerFactory);
    }

    /** @test */
    public function givenARuleStringWhenValidItShouldReturnTheCompiledRule()
    {
        $compiler = m::mock(CompilerInterface::class);
        $compiler->shouldReceive('addLogical')->once();
        $compiler->shouldReceive('addParentheses')->twice();
        $compiler->shouldReceive('addBoolean')->twice();
        $compiler->shouldReceive('getCompiledRule')->once()->andReturn('(1)&1');

        /** @var m\Mock $tokenStream */
        $tokenStream = \Mockery::mock(TokenStream::class);
        $tokenStream->shouldReceive('rewind')->once();
        $tokenStream->shouldReceive('next');
        $tokenStream->shouldReceive('valid')->andReturnUsing(static function () {
            static $count = 0;

            return ++$count < 10;
        });
        $tokenStream->shouldReceive('current')->andReturns(
            new TokenOpeningParenthesis('('),
            new TokenInteger(1),
            new TokenEqual('=='),
            new TokenString('1'),
            new TokenClosingParenthesis(')'),
            new TokenAnd('&&'),
            new TokenInteger(2),
            new TokenGreater('>'),
            new TokenInteger(1),
            new TokenSpace(' '),
            new TokenComment('// true dat!')
        );

        $this->compilerFactory->shouldReceive('create')->once()->andReturn($compiler);
        $this->ast->shouldReceive('getStream')->once()->andReturn($tokenStream);

        $equalExpression = m::mock(BaseExpression::class);
        $equalExpression->shouldReceive('evaluate')->once()->withArgs(function ($leftValue, $rightValue) {
            $this->assertSame(1, $leftValue);
            $this->assertSame('1', $rightValue);

            return true;
        });

        $greaterExpression = m::mock(BaseExpression::class);
        $greaterExpression->shouldReceive('evaluate')->once()->withArgs(function ($leftValue, $rightValue) {
            $this->assertSame(2, $leftValue);
            $this->assertSame(1, $rightValue);

            return true;
        });

        $this->expressionFactory
            ->shouldReceive('createFromOperator')
            ->twice()
            ->with(m::type(BaseToken::class))
            ->andReturns(
                $equalExpression,
                $greaterExpression
            );

        $this->assertSame('(1)&1', $this->parser->parse('(1=="1")&&2>1 // true dat!'));
    }
}
