<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use Closure;
use InvalidArgumentException;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedVariableException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use nicoSWD\Rule\TokenStream\Token\TokenObject;

class TokenStream
{
    private array $functions = [];
    private array $methods = [];
    private array $variables = [];

    public function __construct(
        private readonly TokenizerInterface $tokenizer,
        private readonly TokenFactory $tokenFactory,
        private readonly TokenIteratorFactory $tokenIteratorFactory,
        private readonly CallableUserMethodFactoryInterface $userMethodFactory,
    ) {
    }

    public function getStream(string $rule): TokenIterator
    {
        return $this->tokenIteratorFactory->create($this->tokenizer->tokenize($rule), $this);
    }

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    public function getMethod(string $methodName, BaseToken $token): CallableUserFunctionInterface
    {
        if ($token instanceof TokenObject) {
            return $this->getCallableUserMethod($token, $methodName);
        }

        if (empty($this->methods)) {
            $this->registerMethods();
        }

        if (!isset($this->methods[$methodName])) {
            throw new Exception\UndefinedMethodException();
        }

        return new $this->methods[$methodName]($token);
    }

    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * @throws UndefinedVariableException
     * @throws ParserException
     */
    public function getVariable(string $variableName): BaseToken
    {
        if (!$this->variableExists($variableName)) {
            throw new UndefinedVariableException($variableName);
        }

        return $this->tokenFactory->createFromPHPType($this->variables[$variableName]);
    }

    public function variableExists(string $variableName): bool
    {
        return array_key_exists($variableName, $this->variables);
    }

    /** @throws Exception\UndefinedFunctionException */
    public function getFunction(string $functionName): Closure
    {
        if (empty($this->functions)) {
            $this->registerFunctions();
        }

        if (!isset($this->functions[$functionName])) {
            throw new Exception\UndefinedFunctionException($functionName);
        }

        return $this->functions[$functionName];
    }

    private function registerMethods(): void
    {
        foreach ($this->tokenizer->grammar->getInternalMethods() as $internalMethod) {
            $this->methods[$internalMethod->name] = $internalMethod->class;
        }
    }

    private function registerFunctions(): void
    {
        foreach ($this->tokenizer->grammar->getInternalFunctions() as $function)  {
            $this->registerFunctionClass($function->name, $function->class);
        }
    }

    private function registerFunctionClass(string $functionName, string $className): void
    {
        $this->functions[$functionName] = function (?BaseToken ...$args) use ($className) {
            $function = new $className();

            if (!$function instanceof CallableUserFunctionInterface) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s must be an instance of %s',
                        $className,
                        CallableUserFunctionInterface::class
                    )
                );
            }

            return $function->call(...$args);
        };
    }

    /**
     * @throws Exception\ForbiddenMethodException
     * @throws Exception\UndefinedMethodException
     */
    private function getCallableUserMethod(BaseToken $token, string $methodName): CallableUserFunctionInterface
    {
        return $this->userMethodFactory->create($token, $this->tokenFactory, $methodName);
    }
}
