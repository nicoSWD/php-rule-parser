<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

/**
 * Class Rule
 * @package nicoSWD\Rules
 */
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

    /**
     * @param string $rule
     * @param array  $variables
     */
    public function __construct($rule, array $variables = [])
    {
        $this->rule = (string) $rule;
        $this->parser = new Parser(new Tokenizer());
        $this->evaluator = new Evaluator();

        $this->parser->assignVariables($variables);
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->evaluator->evaluate(
            $this->parsedRule ?:
            $this->parser->parse($this->rule)
        );
    }

    /**
     * @return bool
     */
    public function isFalse()
    {
        return !$this->isTrue();
    }

    /**
     * Tells whether a rule is valid (as in "can be parsed") or not.
     *
     * @return bool
     */
    public function isValid()
    {
        try {
            $this->parsedRule = $this->parser->parse($this->rule);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            return \false;
        }

        return \true;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
