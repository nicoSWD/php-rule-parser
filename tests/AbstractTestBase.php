<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Evaluator;
use nicoSWD\Rules\Parser;
use nicoSWD\Rules\Tokenizer;
use nicoSWD\Rules\Expressions\Factory as ExpressionFactory;

/**
 * Class AbstractTestBase
 */
abstract class AbstractTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var Evaluator
     */
    protected $evaluator;

    /**
     * @return void
     */
    final public function setup()
    {
        $this->parser = new Parser(new Tokenizer(), new ExpressionFactory());
        $this->evaluator = new Evaluator();
    }

    /**
     * @param string  $rule
     * @param mixed[] $variables
     * @return bool
     * @throws \nicoSWD\Rules\Exceptions\ParserException
     */
    protected function evaluate($rule, array $variables = [])
    {
        $this->parser->assignVariables($variables);
        $result = $this->parser->parse($rule);

        return $this->evaluator->evaluate($result);
    }
}
