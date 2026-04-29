<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule;

use Exception;
use nicoSWD\Rule\AST\AstEvaluator;
use nicoSWD\Rule\AST\Node;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\TokenStream\VariableRegistry;

/**
 * The main entry point for the rule parser engine.
 *
 * This class wires up all internal dependencies and provides a clean,
 * easy-to-use API for evaluating rules.
 *
 * Usage:
 * ```php
 * // Simple evaluation
 * $engine = RuleEngine::builder()->build();
 * $result = $engine->evaluate('foo > 5', ['foo' => 10]);
 *
 * // With default variables
 * $engine = RuleEngine::builder()
 *     ->withDefaultVariables(['foo' => 10])
 *     ->build();
 * $result = $engine->evaluate('foo > 5');
 *
 * // Check rule validity
 * $isValid = $engine->isValid('2 == 2 && (1 < 3');
 * $error = $engine->getError('2 == 2 && (1 < 3');
 *
 * // Create a Rule object for repeated use
 * $rule = $engine->createRule('foo > 5', ['foo' => 10]);
 * $rule->isTrue();
 *
 * // Advanced customization
 * $engine = RuleEngine::builder()
 *     ->withGrammar(new MyCustomGrammar())
 *     ->withTokenizer(new MyTokenizer())
 *     ->build();
 * ```
 */
final readonly class RuleEngine
{
    public function __construct(
        private Parser $parser,
        private AstEvaluator $astEvaluator,
        private VariableRegistry $variableRegistry,
        private array $defaultVariables = [],
    ) {
    }

    /**
     * Evaluate a rule string and return whether it's true or false.
     *
     * @param string $rule The rule expression to evaluate
     * @param array $variables Optional variables to use (merged with defaults)
     * @return bool
     * @throws ParserException
     */
    public function evaluate(string $rule, array $variables = []): bool
    {
        $ast = $this->parse($rule, $variables);

        return $this->evaluateNode($ast);
    }

    /**
     * Evaluate a pre-parsed AST node and return whether it's true or false.
     *
     * Use this when you already have a parsed Node (e.g., from a cached parse)
     * to avoid re-parsing the rule string.
     *
     * @param Node $ast The pre-parsed AST node
     * @return bool
     * @throws ParserException
     */
    public function evaluateNode(Node $ast): bool
    {
        return $this->astEvaluator->evaluate($ast);
    }

    /**
     * Evaluate a rule string and return the actual computed result.
     *
     * For pure value expressions (e.g. "5 * 3"), this returns the computed value.
     * For comparison/logical expressions (e.g. "foo > 5"), this returns a bool.
     *
     * @param string $rule The rule expression to evaluate
     * @param array $variables Optional variables to use (merged with defaults)
     * @return mixed
     * @throws ParserException
     */
    public function result(string $rule, array $variables = []): mixed
    {
        $ast = $this->parse($rule, $variables);

        return $this->resolveNode($ast);
    }

    /**
     * Resolve a pre-parsed AST node to its actual computed value, without casting to bool.
     *
     * Use this when you already have a parsed Node (e.g., from a cached parse)
     * to avoid re-parsing the rule string.
     *
     * @param Node $ast The pre-parsed AST node
     * @return mixed
     * @throws ParserException
     */
    public function resolveNode(Node $ast): mixed
    {
        return $this->astEvaluator->resolve($ast);
    }

    /**
     * Check whether a rule string is syntactically valid and can be evaluated.
     *
     * @param string $rule     The rule expression to validate
     * @param array  $variables Optional variables to use (merged with defaults)
     * @return bool
     */
    public function isValid(string $rule, array $variables = []): bool
    {
        return $this->getError($rule, $variables) === '';
    }

    /**
     * Get the error message from the last validation, if any.
     *
     * @param string $rule     The rule expression to validate
     * @param array  $variables Optional variables to use (merged with defaults)
     * @return string Empty string if the rule is valid
     */
    public function getError(string $rule, array $variables = []): string
    {
        try {
            $ast = $this->parse($rule, $variables);
            $this->astEvaluator->evaluate($ast);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return '';
    }

    /**
     * Create a Rule object for the given rule string and variables.
     *
     * This is useful when you need to evaluate the same rule multiple times,
     * or when you want to use the Rule's convenience methods (isTrue, isFalse, etc.).
     *
     * @param string $rule     The rule expression
     * @param array  $variables Optional variables (merged with defaults)
     * @return Rule
     */
    public function createRule(string $rule, array $variables = []): Rule
    {
        return new Rule($rule, $variables, $this);
    }

    /**
     * Parse a rule string into an AST node, applying the given variables.
     *
     * @throws ParserException
     * @internal
     */
    public function parse(string $rule, array $variables = []): Node
    {
        $this->variableRegistry->setVariables(
            array_merge($this->defaultVariables, $variables),
        );

        return $this->parser->parse($rule);
    }

    /**
     * Create a RuleEngineBuilder for advanced customization.
     *
     * @return RuleEngineBuilder
     */
    public static function builder(): RuleEngineBuilder
    {
        return new RuleEngineBuilder();
    }
}
