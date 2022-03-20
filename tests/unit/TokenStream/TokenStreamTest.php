<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\unit\TokenStream;

use ArrayIterator;
use Iterator;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\InternalFunction;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use nicoSWD\Rule\TokenStream\CallableUserMethodFactoryInterface;
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;
use nicoSWD\Rule\TokenStream\Node\BaseNode;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenIteratorFactory;
use nicoSWD\Rule\TokenStream\CallableUserMethodFactory;
use PHPUnit\Framework\TestCase;
use stdClass;

final class TokenStreamTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private readonly TokenStream $tokenStream;
    private readonly TokenFactory|MockInterface $tokenFactory;
    private readonly CallableUserMethodFactory $userMethodFactory;
    private readonly TokenIteratorFactory $tokenStreamFactory;

    protected function setUp(): void
    {
        $this->tokenFactory = Mockery::mock(TokenFactory::class);
        $this->userMethodFactory = new CallableUserMethodFactory();
        $this->tokenStreamFactory = new TokenIteratorFactory();
    }

    /** @test */
    public function givenAFunctionNameWhenValidItShouldReturnTheCorrespondingFunction(): void
    {
        $grammar = $this->createGrammarWithInternalFunctions([new InternalFunction('test', TestFunc::class)]);
        $tokenizer = new Tokenizer($grammar, $this->tokenFactory);

        $tokenStream = new TokenStream(
            $tokenizer,
            $this->tokenFactory,
            $this->tokenStreamFactory,
            $this->userMethodFactory
        );

        /** @var BaseToken $result */
        $result = $tokenStream->getFunction('test')->call(Mockery::mock(BaseNode::class));

        $this->assertSame(234, $result->getValue());
    }

    /** @test */
    public function givenAFunctionNameWhenItDoesNotImplementTheInterfaceItShouldThrowAnException(): void
    {
        $this->expectExceptionMessage(sprintf(
            'stdClass must be an instance of %s',
            CallableUserFunctionInterface::class
        ));

        $grammar = $this->createGrammarWithInternalFunctions([new InternalFunction('test', stdClass::class)]);
        $tokenizer = new Tokenizer($grammar, $this->tokenFactory);

        $tokenStream = new TokenStream(
            $tokenizer,
            $this->tokenFactory,
            $this->tokenStreamFactory,
            $this->userMethodFactory
        );

        $tokenStream->getFunction('test')->call(Mockery::mock(BaseNode::class));
    }

    /** @test */
    public function givenAFunctionNameNotDefinedItShouldThrowAnException(): void
    {
        $this->expectException(UndefinedFunctionException::class);
        $this->expectExceptionMessage('pineapple_pizza');

        $tokenizer = $this->createDummyTokenizer();
        $userMethodFactory = $this->createCallableUserMethodFactory();

        $tokenStream = new TokenStream(
            $tokenizer,
            new TokenFactory(),
            $this->tokenStreamFactory,
            $userMethodFactory,
        );

        $tokenStream->getFunction('pineapple_pizza');
    }

    private function createDummyTokenizer(): TokenizerInterface
    {
        return new class($this->createGrammarWithInternalFunctions()) extends TokenizerInterface {
            public function __construct(
                public Grammar $grammar,
            ) {
            }

            public function tokenize(string $string): Iterator
            {
                return new ArrayIterator([]);
            }
        };
    }

    private function createGrammarWithInternalFunctions(array $internalFunctions = []): Grammar
    {
        return new class($internalFunctions) extends Grammar {
            public function __construct(
                private array $internalFunctions,
            ) {
            }

            public function getDefinition(): array
            {
                return [];
            }

            public function getInternalFunctions(): array
            {
                return $this->internalFunctions;
            }

            public function getInternalMethods(): array
            {
                return [];
            }
        };
    }

    private function createCallableUserMethodFactory(): CallableUserMethodFactoryInterface
    {
        return new class implements CallableUserMethodFactoryInterface {
            public function create(
                BaseToken $token,
                TokenFactory $tokenFactory,
                string $methodName
            ): CallableUserFunctionInterface {
                return new class implements CallableUserFunctionInterface {
                    public function call(?BaseToken ...$param): BaseToken
                    {
                        return Mockery::mock(BaseToken::class);
                    }
                };
            }
        };
    }
}
