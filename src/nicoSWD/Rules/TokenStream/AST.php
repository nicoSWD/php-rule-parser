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
use nicoSWD\Rules\Parser\Exception\ParserException;
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
    public $tokenFactory;
    /** @var TokenStream */
    private $tokenStream;
    /** @var Closure[] */
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
            $this->registerFunctions();
        }

        if (!isset($this->functions[$name])) {
            throw ParserException::undefinedFunction($name);
        }

        return $this->functions[$name];
    }

    public function getMethod(string $methodName, BaseToken $token): CallableUserFunction
    {
        if (empty($this->methods)) {
            $this->registerMethods();
        }

        if (!isset($this->methods[$methodName])) {
            throw new Exception\UndefinedMethodException();
        }

        return new $this->methods[$methodName]($token);
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

    public function variableExists(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    private function registerFunctionClass(string $functionName, string $className)
    {
        $this->functions[$functionName] = function (...$args) use ($className): BaseToken {
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

    private function registerFunctions()
    {
        foreach ($this->tokenizer->getGrammar()->getInternalFunctions() as $functionName => $className) {
            $this->registerFunctionClass($functionName, $className);
        }
    }

    private function registerMethods()
    {
        $this->methods = $this->tokenizer->getGrammar()->getInternalMethods();
    }
}
