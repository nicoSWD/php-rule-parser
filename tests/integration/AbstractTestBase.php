<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\tests\integration;

use nicoSWD\Rule\TokenStream\AST;
use nicoSWD\Rule\Compiler\CompilerFactory;
use nicoSWD\Rule\Evaluator\Evaluator;
use nicoSWD\Rule\Grammar\JavaScript\JavaScript;
use nicoSWD\Rule\Parser\Parser;
use nicoSWD\Rule\Expression\ExpressionFactory;
use nicoSWD\Rule\Tokenizer\Tokenizer;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\TokenStream;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestBase extends TestCase
{
    /** @var Parser */
    protected $parser;
    /** @var Evaluator */
    protected $evaluator;
    /** @var AST */
    protected $ast;

    final protected function setUp()
    {
        $tokenFactory = new TokenFactory();

        $this->ast = new AST(new Tokenizer(new JavaScript(), $tokenFactory), $tokenFactory, new TokenStream());
        $this->parser = new Parser($this->ast, new ExpressionFactory(), new CompilerFactory());
        $this->evaluator = new Evaluator();
    }

    protected function evaluate(string $rule, array $variables = []): bool
    {
        $this->ast->setVariables($variables);
        $result = $this->parser->parse($rule);

        return $this->evaluator->evaluate($result);
    }
}
