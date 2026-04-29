<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\FunctionRegistry;
use nicoSWD\Rule\TokenStream\MethodRegistry;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\VariableRegistry;

final readonly class AstEvaluator
{
    private EvaluationContext $context;

    public function __construct(
        VariableRegistry $variableRegistry,
        FunctionRegistry $functionRegistry,
        MethodRegistry   $methodRegistry,
        TokenFactory     $tokenFactory,
    ) {
        $this->context = new EvaluationContext($variableRegistry, $functionRegistry, $methodRegistry, $tokenFactory);
    }

    /**
     * @throws ParserException
     */
    public function evaluate(Node $node): bool
    {
        return (bool) $this->resolve($node);
    }

    /**
     * Resolve a node to its actual computed value, without casting to bool.
     *
     * @throws ParserException
     */
    public function resolve(Node $node): mixed
    {
        return $node->evaluate($this->context);
    }
}
