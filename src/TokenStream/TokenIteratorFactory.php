<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream;

use Iterator;

final readonly class TokenIteratorFactory
{
    public function __construct(
        private VariableRegistry $variableRegistry,
        private FunctionRegistry $functionRegistry,
        private MethodRegistry   $methodRegistry,
    ) {
    }

    public function create(Iterator $stack): TokenIterator
    {
        return new TokenIterator($stack, $this->variableRegistry, $this->functionRegistry, $this->methodRegistry);
    }
}
