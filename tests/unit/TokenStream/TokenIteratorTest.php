<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream;

use ArrayIterator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedMethodException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedVariableException;
use nicoSWD\Rule\TokenStream\FunctionRegistry;
use nicoSWD\Rule\TokenStream\MethodRegistry;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFunction;
use nicoSWD\Rule\TokenStream\Token\TokenMethod;
use nicoSWD\Rule\TokenStream\Token\TokenString;
use nicoSWD\Rule\TokenStream\Token\TokenVariable;
use nicoSWD\Rule\TokenStream\TokenIterator;
use nicoSWD\Rule\TokenStream\VariableRegistry;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class TokenIteratorTest extends TestCase
{
    use MockeryPHPUnitIntegration;
    
    private ArrayIterator|MockInterface $stack;
    private VariableRegistry|MockInterface $variableRegistry;
    private FunctionRegistry|MockInterface $functionRegistry;
    private MethodRegistry|MockInterface $methodRegistry;
    private TokenIterator $tokenIterator;

    protected function setUp(): void
    {
        $this->stack = \Mockery::mock(ArrayIterator::class);
        $this->variableRegistry = \Mockery::mock(VariableRegistry::class);
        $this->functionRegistry = \Mockery::mock(FunctionRegistry::class);
        $this->methodRegistry = \Mockery::mock(MethodRegistry::class);

        $this->tokenIterator = new TokenIterator(
            $this->stack,
            $this->variableRegistry,
            $this->functionRegistry,
            $this->methodRegistry,
        );
    }

    #[Test]
    public function givenAStackWhenNotEmptyItShouldBeIterable()
    {
        $this->stack->shouldReceive('rewind');
        $this->stack->shouldReceive('valid')->andReturn(true, true, true, false);
        $this->stack->shouldReceive('key')->andReturn(1, 2, 3);
        $this->stack->shouldReceive('next');
        $this->stack->shouldReceive('current')->times(3)->andReturn(
            new TokenString('a'),
            new TokenMethod('.foo('),
            new TokenString('b')
        );

        foreach ($this->tokenIterator as $value) {
            $this->assertInstanceOf(BaseToken::class, $value);
        }
    }

    #[Test]
    public function givenATokenStackItShouldBeAccessibleViaGetter()
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->tokenIterator->getStack());
    }

    #[Test]
    public function givenAVariableNameWhenFoundItShouldReturnItsValue()
    {
        $this->variableRegistry->shouldReceive('get')->once()->with('foo')->andReturn(new TokenVariable('bar'));

        $token = $this->tokenIterator->getVariable('foo');
        $this->assertInstanceOf(TokenVariable::class, $token);
    }

    #[Test]
    public function givenAVariableNameWhenNotFoundItShouldThrowAnException()
    {
        $this->expectException(ParserException::class);

        $this->variableRegistry->shouldReceive('get')->once()->with('foo')->andThrow(new UndefinedVariableException());
        $this->stack->shouldReceive('current')->once()->andReturn(new TokenVariable('nope'));

        $this->tokenIterator->getVariable('foo');
    }

    #[Test]
    public function givenAFunctionNameWhenFoundItShouldACallableClosure()
    {
        $this->functionRegistry->shouldReceive('get')->once()->with('foo')->andReturn(fn () => 42);

        $function = $this->tokenIterator->getFunction('foo');
        $this->assertSame(42, $function());
    }

    #[Test]
    public function givenAFunctionNameWhenNotFoundItShouldThrowAnException()
    {
        $this->expectException(ParserException::class);

        $this->functionRegistry->shouldReceive('get')->once()->with('foo')->andThrow(new UndefinedFunctionException());
        $this->stack->shouldReceive('current')->once()->andReturn(new TokenFunction('nope('));

        $this->tokenIterator->getFunction('foo');
    }

    #[Test]
    public function givenAMethodNameWhenFoundItShouldReturnAnInstanceOfCallableFunction()
    {
        $token = new TokenString('bar');
        $callableFunction = \Mockery::mock(CallableFunction::class);

        $this->methodRegistry->shouldReceive('get')->once()->with('foo', $token)->andReturn($callableFunction);

        $method = $this->tokenIterator->getMethod('foo', $token);

        $this->assertInstanceOf(CallableFunction::class, $method);
    }

    #[Test]
    public function givenAMethodNameWhenNotFoundItShouldThrowAnException()
    {
        $this->expectException(ParserException::class);

        $token = new TokenString('bar');
        $this->methodRegistry->shouldReceive('get')->once()->with('foo', $token)->andThrow(new UndefinedMethodException());
        $this->stack->shouldReceive('current')->once()->andReturn(new TokenFunction('bar'));

        $this->tokenIterator->getMethod('foo', $token);
    }
}
