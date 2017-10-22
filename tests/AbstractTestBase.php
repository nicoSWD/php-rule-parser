<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
use nicoSWD\Rules\AST;
use nicoSWD\Rules\Evaluator;
use nicoSWD\Rules\Grammar\JavaScript\JavaScript;
use nicoSWD\Rules\Parser;
use nicoSWD\Rules\Compiler;
use nicoSWD\Rules\Tokenizer;
use nicoSWD\Rules\Expressions\ExpressionFactory as ExpressionFactory;
use nicoSWD\Rules\Tokens\TokenFactory;
use nicoSWD\Rules\TokenStream;

abstract class AbstractTestBase extends \PHPUnit\Framework\TestCase
{
    /** @var Parser */
    protected $parser;

    /** @var Evaluator */
    protected $evaluator;

    /** @var AST */
    protected $ast;

    /** @return void */
    final protected function setUp()
    {
        $tokenFactory = new TokenFactory();
        $tokenStream = new TokenStream();

        $this->ast = new AST(new Tokenizer(new JavaScript(), $tokenFactory), $tokenFactory, $tokenStream);
        $this->parser = new Parser($this->ast, new ExpressionFactory(), new Compiler());
        $this->evaluator = new Evaluator();
    }

    protected function evaluate(string $rule, array $variables = []): bool
    {
        $this->ast->setVariables($variables);
        $result = $this->parser->parse($rule);

        return $this->evaluator->evaluate($result);
    }
}
