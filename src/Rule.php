<?php
/**
 * Created by PhpStorm.
 * User: Nico
 * Date: 17/07/15
 * Time: 20:48
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
     * @param string $rule
     * @param array  $variables
     */
    public function __construct($rule, array $variables = [])
    {
        $this->rule = (string) $rule;
        $this->parser = new Parser(new Tokenizer());
        $this->parser->assignVariables($variables);
        $this->evaluator = new Evaluator();
    }

    /**
     * @return bool
     */
    public function isTrue()
    {
        return $this->evaluator->evaluate(
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
}

$string = '
    /**
     * This is a test rule with comments
     */

     // This is true
     2 < 3 and (
        // foo is not 4
        foo is 4
        // but bar is > 6
        or bar > 6
     )';

$vars = [
    'foo' => 5,
    'bar' => 7
];

$rule = new Rule($string, $vars);

if ($rule->isTrue()) {
    echo 'yes';
}
