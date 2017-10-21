<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\Evaluator;
use nicoSWD\Rules\Grammar\JavaScript\JavaScript;
use nicoSWD\Rules\Parser;
use nicoSWD\Rules\RuleGenerator;
use nicoSWD\Rules\Tokenizer;
use nicoSWD\Rules\Expressions\ExpressionFactory as ExpressionFactory;
use nicoSWD\Rules\Tokens\TokenFactory;

abstract class AbstractTestBase extends \PHPUnit\Framework\TestCase
{
    /** @var Parser */
    protected $parser;

    /** @var Evaluator */
    protected $evaluator;

    /** @return void */
    final protected function setUp()
    {
        $this->parser = new Parser(new Tokenizer(new JavaScript(), new TokenFactory()), new ExpressionFactory(), new RuleGenerator());
        $this->evaluator = new Evaluator();
    }

    protected function evaluate(string $rule, array $variables = []): bool
    {
        $this->parser->assignVariables($variables);
        $result = $this->parser->parse($rule);

        return $this->evaluator->evaluate($result);
    }
}
