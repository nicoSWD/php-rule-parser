<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules;

use Closure;
use Exception;

class Rule
{
    /**
     * @var string
     */
    private $rule;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var Evaluator
     */
    private $evaluator;

    /**
     * @var string
     */
    private $parsedRule = '';

    /**
     * @var string
     */
    private $error = '';

    public function __construct(string $rule, array $variables = [])
    {
        $this->rule = $rule;
        $this->parser = new Parser(new Tokenizer(), new Expressions\Factory());
        $this->evaluator = new Evaluator();

        $this->parser->assignVariables($variables);
    }

    public function isTrue() : bool
    {
        return $this->evaluator->evaluate(
            $this->parsedRule ?:
            $this->parser->parse($this->rule)
        );
    }

    public function isFalse() : bool
    {
        return !$this->isTrue();
    }

    /**
     * Tells whether a rule is valid (as in "can be parsed without error") or not.
     */
    public function isValid() : bool
    {
        try {
            $this->parsedRule = $this->parser->parse($this->rule);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }

        return true;
    }

    public function registerFunction(string $name, Closure $callback)
    {
        $this->parser->registerFunction($name, $callback);
    }

    public function registerToken(string $class, string $regex, int $priority = 10)
    {
        $this->parser->registerToken($class, $regex, $priority);
    }

    public function getError() : string
    {
        return $this->error;
    }
}
