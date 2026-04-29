<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule;

use Exception;
use nicoSWD\Rule\AST\AstEvaluator;
use nicoSWD\Rule\AST\Node;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\Tokenizer\Lexer;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use nicoSWD\Rule\TokenStream\FunctionRegistry;
use nicoSWD\Rule\TokenStream\MethodRegistry;
use nicoSWD\Rule\TokenStream\ObjectMethodCallerFactory;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenIteratorFactory;
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
 * $engine = new RuleEngine();
 * $result = $engine->evaluate('foo > 5', ['foo' => 10]);
 *
 * // With default variables
 * $engine = new RuleEngine(defaultVariables: ['foo' => 10]);
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
    private TokenFactory $tokenFactory;
    private TokenizerInterface $tokenizer;
    private VariableRegistry $variableRegistry;
    private FunctionRegistry $functionRegistry;
    private MethodRegistry $methodRegistry;
    private Parser $parser;
    private AstEvaluator $astEvaluator;

    public function __construct(
        ?TokenizerInterface $tokenizer = null,
        ?Grammar $grammar = null,
        private array $defaultVariables = [],
    ) {
        $this->tokenFactory = new TokenFactory();
        $grammar ??= new JavaScript();
        $this->tokenizer = $tokenizer ?? new Lexer($grammar, $this->tokenFactory);

        $this->variableRegistry = new VariableRegistry([], $this->tokenFactory);
        $this->functionRegistry = new FunctionRegistry($grammar);
        $this->methodRegistry = new MethodRegistry($grammar, $this->tokenFactory, new ObjectMethodCallerFactory());

        $this->parser = new Parser(
            new TokenIteratorFactory(
                $this->variableRegistry,
                $this->functionRegistry,
                $this->methodRegistry,
            ),
            $this->tokenizer,
        );

        $this->astEvaluator = new AstEvaluator(
            $this->variableRegistry,
            $this->functionRegistry,
            $this->methodRegistry,
            $this->tokenFactory,
        );
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
        try {
            $ast = $this->parse($rule, $variables);
            $this->astEvaluator->evaluate($ast);
        } catch (Exception) {
            return false;
        }

        return true;
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
