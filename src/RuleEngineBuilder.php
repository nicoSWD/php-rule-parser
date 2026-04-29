<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule;

use nicoSWD\Rule\AST\AstEvaluator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\Tokenizer\Lexer;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;
use nicoSWD\Rule\TokenStream\FunctionRegistry;
use nicoSWD\Rule\TokenStream\MethodRegistry;
use nicoSWD\Rule\TokenStream\ObjectMethodCallerFactory;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\VariableRegistry;

/**
 * Builder for creating a RuleEngine with custom configuration.
 *
 * Usage:
 * ```php
 * $engine = RuleEngine::builder()
 *     ->withGrammar(new MyCustomGrammar())
 *     ->withTokenizer(new MyTokenizer())
 *     ->withDefaultVariables(['env' => 'prod'])
 *     ->build();
 * ```
 */
final class RuleEngineBuilder
{
    private ?Grammar $grammar = null;
    private ?TokenizerInterface $tokenizer = null;
    private array $defaultVariables = [];

    /**
     * Create a new builder instance.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Set a custom grammar for the rule engine.
     *
     * The grammar defines the syntax rules (operators, keywords, functions, methods).
     *
     * @param Grammar $grammar
     * @return $this
     */
    public function withGrammar(Grammar $grammar): self
    {
        $this->grammar = $grammar;

        return $this;
    }

    /**
     * Set a custom tokenizer for the rule engine.
     *
     * The tokenizer converts a rule string into a stream of tokens.
     *
     * @param TokenizerInterface $tokenizer
     * @return $this
     */
    public function withTokenizer(TokenizerInterface $tokenizer): self
    {
        $this->tokenizer = $tokenizer;

        return $this;
    }

    /**
     * Set default variables that will be available for all rule evaluations.
     *
     * These variables can be overridden on a per-evaluation basis.
     *
     * @param array $variables
     * @return $this
     */
    public function withDefaultVariables(array $variables): self
    {
        $this->defaultVariables = $variables;

        return $this;
    }

    /**
     * Build the RuleEngine with the configured options.
     *
     * @return RuleEngine
     */
    public function build(): RuleEngine
    {
        $tokenFactory = new TokenFactory();
        $grammar = $this->grammar ?? new JavaScript();
        $tokenizer = $this->tokenizer ?? new Lexer($grammar, $tokenFactory);

        $variableRegistry = new VariableRegistry([], $tokenFactory);
        $functionRegistry = new FunctionRegistry($grammar);
        $methodRegistry = new MethodRegistry($grammar, $tokenFactory, new ObjectMethodCallerFactory());

        $parser = new Parser(
            $tokenizer,
        );

        $astEvaluator = new AstEvaluator(
            $variableRegistry,
            $functionRegistry,
            $methodRegistry,
            $tokenFactory,
        );

        return new RuleEngine(
            parser: $parser,
            astEvaluator: $astEvaluator,
            variableRegistry: $variableRegistry,
            defaultVariables: $this->defaultVariables,
        );
    }
}
