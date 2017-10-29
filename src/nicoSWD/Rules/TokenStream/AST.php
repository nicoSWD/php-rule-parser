<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\TokenStream;

use Closure;
use InvalidArgumentException;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Grammar\CallableUserFunction;
use nicoSWD\Rules\Tokenizer\TokenizerInterface;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenFactory;
use nicoSWD\Rules\TokenStream\Exception\UndefinedVariableException;

class AST
{
    /** @var TokenizerInterface */
    private $tokenizer;

    /** @var TokenFactory */
    private $tokenFactory;

    /** @var TokenStream */
    private $tokenStream;

    /** @var Callable[] */
    private $functions = [];

    /** @var mixed[] */
    private $variables = [];

    /** @var string[] */
    private $methods = [];

    public function __construct(
        TokenizerInterface $tokenizer,
        TokenFactory $tokenFactory,
        TokenStream $tokenStream
    ) {
        $this->tokenizer = $tokenizer;
        $this->tokenFactory = $tokenFactory;
        $this->tokenStream = $tokenStream;
    }

    public function getStream(string $rule): TokenStream
    {
        return $this->tokenStream->create($this->tokenizer->tokenize($rule), $this);
    }

    public function getFunction(string $name): Closure
    {
        if (empty($this->functions)) {
            $this->registerFunctions($this->tokenizer->getGrammar()->getInternalFunctions());
        }

        if (!isset($this->functions[$name])) {
            throw new ParserException(sprintf(
                '%s is not defined',
                $name
            ));
        }

        return $this->functions[$name];
    }

    public function getMethod(string $methodName, BaseToken $token): CallableUserFunction
    {
        if (empty($this->methods)) {
            $this->registerMethods($this->tokenizer->getGrammar()->getInternalMethods());
        }

        if (!isset($this->methods[$methodName])) {
            throw new Exception\UndefinedMethodException();
        }

        $method = new $this->methods[$methodName]($token);

        if (!$method instanceof CallableUserFunction) {
            throw new InvalidArgumentException(
                sprintf(
                    "%s must be an instance of %s",
                    $methodName,
                    CallableUserFunction::class
                )
            );
        }

        return $method;
    }

    public function variableExists(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    public function getVariable(string $name): BaseToken
    {
        if (!$this->variableExists($name)) {
            throw new UndefinedVariableException();
        }

        return $this->tokenFactory->createFromPHPType($this->variables[$name]);
    }

    private function registerFunctionClass(string $functionName, string $className)
    {
        $this->functions[$functionName] = function (...$args) use ($className): BaseToken {
            /** @var CallableUserFunction $function */
            $function = new $className();

            if (!$function instanceof CallableUserFunction) {
                throw new InvalidArgumentException(
                    sprintf(
                        "%s must be an instance of %s",
                        $className,
                        CallableUserFunction::class
                    )
                );
            }

            return $function->call(...$args);
        };
    }

    private function registerFunctions(array $functions)
    {
        foreach ($functions as $functionName => $function) {
            $this->registerFunctionClass($functionName, $function);
        }
    }

    private function registerMethods(array $methods)
    {
        $this->methods = $methods;
    }
}
