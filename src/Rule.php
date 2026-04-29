<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule;

use nicoSWD\Rule\AST\Node;
use nicoSWD\Rule\Parser\Exception\ParserException;

/**
 * Convenience class for evaluating a single rule expression.
 *
 * This class provides a simple, familiar API for evaluating rules.
 * For more advanced use cases (multiple rules, custom configuration),
 * use RuleEngine directly.
 *
 * Usage:
 * ```php
 * // Simple usage (creates a default RuleEngine internally)
 * $rule = new Rule('foo > 5', ['foo' => 10]);
 * $rule->isTrue();  // true
 *
 * // With a shared engine (more efficient for multiple rules)
 * $engine = new RuleEngine(defaultVariables: ['foo' => 10]);
 * $rule1 = new Rule('foo > 5', engine: $engine);
 * $rule2 = new Rule('foo < 3', engine: $engine);
 * ```
 */
class Rule
{
    private readonly RuleEngine $engine;
    private readonly string $rule;
    private readonly array $variables;
    private ?Node $ast = null;
    public string $error = '' {
        get {
            return $this->error;
        }
    }

    public function __construct(
        string $rule,
        array $variables = [],
        ?RuleEngine $engine = null,
    ) {
        $this->engine = $engine ?? new RuleEngine();
        $this->rule = $rule;
        $this->variables = $variables;
    }

    /**
     * @throws ParserException
     */
    public function isTrue(): bool
    {
        if ($this->ast === null) {
            $this->ast = $this->engine->parse($this->rule, $this->variables);
        }

        return $this->engine->evaluate($this->rule, $this->variables);
    }

    /**
     * @throws ParserException
     */
    public function isFalse(): bool
    {
        return !$this->isTrue();
    }

    /**
     * Evaluate the rule and return the actual computed result.
     *
     * For pure value expressions (e.g. "5 * 3"), this returns the computed value.
     * For comparison/logical expressions (e.g. "foo > 5"), this returns a bool.
     *
     * @return mixed
     * @throws ParserException
     */
    public function result(): mixed
    {
        if ($this->ast === null) {
            $this->ast = $this->engine->parse($this->rule, $this->variables);
        }

        return $this->engine->result($this->rule, $this->variables);
    }

    /**
     * Tells whether a rule is valid (as in "can be parsed and evaluated without error") or not.
     */
    public function isValid(): bool
    {
        $this->error = $this->engine->getError($this->rule, $this->variables);

        return $this->error === '';
    }

}
