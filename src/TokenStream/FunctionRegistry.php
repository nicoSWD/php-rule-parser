<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream;

use Closure;
use InvalidArgumentException;
use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Exception\UndefinedFunctionException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

class FunctionRegistry
{
    /** @var array<string, Closure> */
    private array $functions;

    public function __construct(
        private readonly Grammar $grammar,
    ) {
        $this->functions = [];
        $this->registerFunctions();
    }

    /** @throws UndefinedFunctionException */
    public function get(string $functionName): Closure
    {
        if (!isset($this->functions[$functionName])) {
            throw new UndefinedFunctionException($functionName);
        }

        return $this->functions[$functionName];
    }

    private function registerFunctions(): void
    {
        foreach ($this->grammar->getInternalFunctions() as $internalCallable) {
            $this->registerFunctionClass($internalCallable->name, $internalCallable->class);
        }
    }

    private function registerFunctionClass(string $functionName, string $className): void
    {
        $this->functions[$functionName] = function (?BaseToken ...$args) use ($className) {
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

            return $function->call(...$args);
        };
    }
}
