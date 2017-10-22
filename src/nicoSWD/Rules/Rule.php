<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use Exception;
use nicoSWD\Rules\Grammar\JavaScript\JavaScript;
use nicoSWD\Rules\Tokens\TokenFactory;

class Rule
{
    /** @var string */
    private $rule;

    /** @var Parser */
    private $parser;

    /** @var Evaluator */
    private $evaluator;

    /** @var string */
    private $parsedRule = '';

    /** @var string */
    private $error = '';

    public function __construct(string $rule, array $variables = [])
    {
        $this->rule = $rule;

        $grammar = new JavaScript();
        $tokenFactory = new TokenFactory();
        $ruleGenerator = new Compiler();
        $expressionFactory = new Expressions\ExpressionFactory();

        $tokenizer = new Tokenizer(
            $grammar,
            $tokenFactory
        );

        $ast = new AST($tokenizer, $tokenFactory, new TokenStream());
        $ast->setVariables($variables);

        $this->parser = new Parser(
            $ast,
            $expressionFactory,
            $ruleGenerator
        );

        $this->evaluator = new Evaluator();
    }

    public function isTrue(): bool
    {
        return $this->evaluator->evaluate(
            $this->parsedRule ?:
            $this->parser->parse($this->rule)
        );
    }

    public function isFalse(): bool
    {
        return !$this->isTrue();
    }

    /**
     * Tells whether a rule is valid (as in "can be parsed without error") or not.
     */
    public function isValid(): bool
    {
        try {
            $this->parsedRule = $this->parser->parse($this->rule);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

    public function getError(): string
    {
        return $this->error;
    }
}
