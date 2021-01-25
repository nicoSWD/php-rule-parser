<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use nicoSWD\Rule\TokenStream\AST;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;
use nicoSWD\Rule\TokenStream\Node\BaseNode;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenStreamFactory;
use nicoSWD\Rule\TokenStream\CallableUserMethodFactory;
use PHPUnit\Framework\TestCase;

final class ASTTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private TokenizerInterface|MockInterface $tokenizer;
    private TokenFactory|MockInterface $tokenFactory;
    private TokenStreamFactory|MockInterface $tokenStreamFactory;
    private AST $ast;
    private CallableUserMethodFactory $userMethodFactory;

    protected function setUp(): void
    {
        $this->tokenizer = \Mockery::mock(TokenizerInterface::class);
        $this->tokenFactory = \Mockery::mock(TokenFactory::class);
        $this->tokenStreamFactory = \Mockery::mock(TokenStreamFactory::class);
        $this->userMethodFactory = new CallableUserMethodFactory();

        $this->ast = new AST(
            $this->tokenizer,
            $this->tokenFactory,
            $this->tokenStreamFactory,
            $this->userMethodFactory
        );
    }

    /** @test */
    public function givenAFunctionNameWhenValidItShouldReturnTheCorrespondingFunction(): void
    {
        $grammar = \Mockery::mock(Grammar::class);
        $grammar->shouldReceive('getInternalFunctions')->once()->andReturn(['test' => TestFunc::class]);
        $this->tokenizer->shouldReceive('getGrammar')->once()->andReturn($grammar);

        /** @var BaseToken $result */
        $result = $this->ast->getFunction('test')->call(\Mockery::mock(BaseNode::class));

        $this->assertSame(234, $result->getValue());
    }

    /** @test */
    public function givenAFunctionNameWhenItDoesNotImplementTheInterfaceItShouldThrowAnException(): void
    {
        $this->expectExceptionMessage(sprintf(
            'stdClass must be an instance of %s',
            CallableUserFunctionInterface::class
        ));

        $grammar = \Mockery::mock(Grammar::class);
        $grammar->shouldReceive('getInternalFunctions')->once()->andReturn(['test' => \stdClass::class]);
        $this->tokenizer->shouldReceive('getGrammar')->once()->andReturn($grammar);

        $this->ast->getFunction('test')->call(\Mockery::mock(BaseNode::class));
    }

    /** @test */
    public function givenAFunctionNameNotDefinedItShouldThrowAnException(): void
    {
        $this->expectException(UndefinedFunctionException::class);
        $this->expectExceptionMessage('pineapple_pizza');

        $grammar = \Mockery::mock(Grammar::class);
        $grammar->shouldReceive('getInternalFunctions')->once()->andReturn([]);
        $this->tokenizer->shouldReceive('getGrammar')->once()->andReturn($grammar);

        $this->ast->getFunction('pineapple_pizza')->call(\Mockery::mock(BaseNode::class));
    }
}
