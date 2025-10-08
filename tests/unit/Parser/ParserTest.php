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
use nicoSWD\Rule\Compiler\StandardCompiler;
use nicoSWD\Rule\Parser\EvaluatableExpressionFactory;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\Compiler\CompilerFactoryInterface;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\TokenStream\Token;
use nicoSWD\Rule\TokenStream\TokenIterator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

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

        $compiler = new StandardCompiler();
        $arrayIterator = new ArrayIterator($tokens);
        $tokenIterator = new TokenIterator($arrayIterator, $this->tokenStream);

        $this->compilerFactory->shouldReceive('create')->once()->andReturn($compiler);
        $this->tokenStream->shouldReceive('getStream')->once()->andReturn($tokenIterator);

        $this->assertSame('(1)&1', $this->parser->parse('(1=="1")&&2>1 // true dat!'));
    }
}
