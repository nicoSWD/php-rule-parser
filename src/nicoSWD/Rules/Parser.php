<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use nicoSWD\Rules\Compiler\CompilerInterface;
use nicoSWD\Rules\Compiler\CompilerFactoryInterface;
use nicoSWD\Rules\Compiler\Exception\MissingOperatorException;
use nicoSWD\Rules\Expressions\ExpressionFactoryInterface;
use nicoSWD\Rules\Tokens\BaseToken;
use SplStack;

class Parser
{
    /** @var TokenStream\AST */
    private $ast;

    /** @var ExpressionFactoryInterface */
    private $expressionFactory;

    /** @var CompilerFactoryInterface */
    private $compilerFactory;

    /** @var null|BaseToken */
    private $operator = null;

    public function __construct(
        TokenStream\AST $ast,
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
            switch ($token->getType()) {
                case Tokens\TokenType::VALUE:
                    $values->push($token->getValue());
                    break;
                case Tokens\TokenType::LOGICAL:
                    $compiler->addLogical($token);
                    continue 2;
                case Tokens\TokenType::PARENTHESIS:
                    $compiler->addParentheses($token);
                    continue 2;
                case Tokens\TokenType::OPERATOR:
                    $this->assignOperator($token, $values);
                    continue 2;
                case Tokens\TokenType::COMMENT:
                case Tokens\TokenType::SPACE:
                    continue 2;
                default:
                    throw Exceptions\ParserException::unknownToken($token);
            }

            $this->evaluateExpression($values, $compiler);
        }

        return $compiler->getCompiledRule();
    }

    private function assignOperator(BaseToken $token, SplStack $values)
    {
        if (isset($this->operator)) {
            throw Exceptions\ParserException::unexpectedToken($token);
        } elseif ($values->isEmpty()) {
            throw Exceptions\ParserException::incompleteExpression($token);
        }

        $this->operator = $token;
    }

    private function evaluateExpression(SplStack $values, CompilerInterface $compiler)
    {
        if (!isset($this->operator) || $values->count() !== 2) {
            return;
        }

        list ($rightValue, $leftValue) = $values;

        try {
            $expression = $this->expressionFactory->createFromOperator($this->operator);

            $compiler->addBoolean(
                $expression->evaluate($leftValue, $rightValue)
            );
        } catch (MissingOperatorException $e) {
            throw new Exceptions\ParserException('Missing operator');
        }

        do {
            $values->pop();
        } while (!$values->isEmpty());

        unset($this->operator);
    }
}
