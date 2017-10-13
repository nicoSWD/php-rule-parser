<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Evaluator;
use nicoSWD\Rules\Parser;
use nicoSWD\Rules\Tokenizer;
use nicoSWD\Rules\Expressions\Factory as ExpressionFactory;

abstract class AbstractTestBase extends \PHPUnit\Framework\TestCase
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
    final protected function setUp()
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
    protected function evaluate(string $rule, array $variables = [])
    {
        $this->parser->assignVariables($variables);
        $result = $this->parser->parse($rule);

        return $this->evaluator->evaluate($result);
    }
}
