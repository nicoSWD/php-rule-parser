<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Parser;

use nicoSWD\Rule\Compiler\CompilerInterface;
use nicoSWD\Rule\Compiler\CompilerFactoryInterface;
use nicoSWD\Rule\Compiler\Exception\MissingOperatorException;
use nicoSWD\Rule\Expression\ExpressionFactoryInterface;
use nicoSWD\Rule\TokenStream\AST;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use SplStack;

class Parser
{
    /** @var AST */
    private $ast;
    /** @var ExpressionFactoryInterface */
    private $expressionFactory;
    /** @var CompilerFactoryInterface */
    private $compilerFactory;
    /** @var null|BaseToken */
    private $operator = null;

    public function __construct(
        AST $ast,
        ExpressionFactoryInterface $expressionFactory,
        CompilerFactoryInterface $compilerFactory
    ) {
        $this->ast = $ast;
        $this->expressionFactory = $expressionFactory;
        $this->compilerFactory = $compilerFactory;
    }

    public function parse(string $rule): string
    {
        $compiler = $this->compilerFactory->create();
        $this->operator = null;
        $values = new SplStack();

        foreach ($this->ast->getStream($rule) as $token) {
            if ($token->isValue()) {
                $values->push($token->getValue());
            } elseif ($token->isWhitespace()) {
                continue;
            } elseif ($token->isOperator()) {
                $this->assignOperator($token, $values);
            } elseif ($token->isLogical()) {
                $compiler->addLogical($token);
            } elseif ($token->isParenthesis()) {
                $compiler->addParentheses($token);
            } else {
                throw Exception\ParserException::unknownToken($token);
            }

            $this->evaluateExpression($values, $compiler);
        }

        return $compiler->getCompiledRule();
    }

    private function assignOperator(BaseToken $token, SplStack $values)
    {
        if (isset($this->operator)) {
            throw Exception\ParserException::unexpectedToken($token);
        } elseif ($values->isEmpty()) {
            throw Exception\ParserException::incompleteExpression($token);
        }

        $this->operator = $token;
    }

    private function evaluateExpression(SplStack $values, CompilerInterface $compiler)
    {
        if (!isset($this->operator) || $values->count() !== 2) {
            return;
        }

        list($rightValue, $leftValue) = $values;

        try {
            $expression = $this->expressionFactory->createFromOperator($this->operator);

            $compiler->addBoolean(
                $expression->evaluate($leftValue, $rightValue)
            );
        } catch (MissingOperatorException $e) {
            throw new Exception\ParserException('Missing operator');
        }

        do {
            $values->pop();
        } while (!$values->isEmpty());

        unset($this->operator);
    }
}
