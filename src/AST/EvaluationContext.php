<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\TokenStream\FunctionRegistry;
use nicoSWD\Rule\TokenStream\MethodRegistry;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\VariableRegistry;

final readonly class EvaluationContext
{
    public function __construct(
        public VariableRegistry  $variableRegistry,
        public FunctionRegistry  $functionRegistry,
        public MethodRegistry    $methodRegistry,
        public TokenFactory      $tokenFactory,
    ) {
    }
}
