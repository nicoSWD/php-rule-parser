<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream;

use InvalidArgumentException;
use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;

class FunctionRegistry
{
    /** @var array<string, class-string> */
    private array $functions;

    /** @var array<string, CallableInterface> */
    private array $instances = [];

    public function __construct(
        private readonly Grammar $grammar,
    ) {
        $this->functions = [];
        $this->registerFunctions();
    }

    /** @throws UndefinedFunctionException */
    public function get(string $functionName): CallableInterface
    {
        if (!isset($this->functions[$functionName])) {
            throw new UndefinedFunctionException($functionName);
        }

        return $this->instances[$functionName] ??= $this->createInstance($functionName);
    }

    private function createInstance(string $functionName): CallableInterface
    {
        $className = $this->functions[$functionName];
        $function = new $className();

        if (!$function instanceof CallableInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s must be an instance of %s',
                    $className,
                    CallableInterface::class
                )
            );
        }

        return $function;
    }

    private function registerFunctions(): void
    {
        foreach ($this->grammar->getInternalFunctions() as $internalCallable) {
            $this->functions[$internalCallable->name] = $internalCallable->class;
        }
    }
}
