<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\Parser;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use nicoSWD\Rule\Compiler\StandardCompiler;
use nicoSWD\Rule\Parser\EvaluatableExpressionFactory;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\Compiler\CompilerFactoryInterface;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\TokenIterator;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private TokenStream|m\Mock $tokenStream;
    private EvaluatableExpressionFactory $expressionFactory;
    private CompilerFactoryInterface|m\Mock $compilerFactory;
    private Parser $parser;

    protected function setUp(): void
    {
        $this->tokenStream = m::mock(TokenStream::class);
        $this->compilerFactory = m::mock(CompilerFactoryInterface::class);

        $this->parser = new Parser($this->tokenStream, new EvaluatableExpressionFactory(), $this->compilerFactory);
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

        $compiler = new StandardCompiler();

        /** @var m\MockInterface $tokenStream */
        $tokenStream = \Mockery::mock(TokenIterator::class);
        $tokenStream->shouldReceive('rewind')->once();
        $tokenStream->shouldReceive('next');
        $tokenStream->shouldReceive('current')->andReturn(...$tokens);
        $tokenStream->shouldReceive('valid')->andReturnUsing(function () use (&$tokens) {
            return !!next($tokens);
        });

        $this->compilerFactory->shouldReceive('create')->once()->andReturn($compiler);
        $this->tokenStream->shouldReceive('getStream')->once()->andReturn($tokenStream);

        $this->assertSame('(1)&1', $this->parser->parse('(1=="1")&&2>1 // true dat!'));
    }
}
