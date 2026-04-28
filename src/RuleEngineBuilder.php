<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule;

use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Tokenizer\TokenizerInterface;

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
        return new RuleEngine(
            tokenizer: $this->tokenizer,
            grammar: $this->grammar,
            defaultVariables: $this->defaultVariables,
        );
    }
}
