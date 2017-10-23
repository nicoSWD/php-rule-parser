<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use nicoSWD\Rules\Expressions\ExpressionFactory;
use nicoSWD\Rules\Tokens\BaseToken;
use SplStack;

class Parser
{
    /** @var AST */
    private $ast;

    /** @var ExpressionFactory */
    private $expressionFactory;

    /** @var Compiler */
    private $compiler;

    /** @var null|BaseToken */
    private $operator = null;

    public function __construct(AST $ast, ExpressionFactory $expressionFactory, Compiler $compiler)
    {
        $this->ast = $ast;
        $this->expressionFactory = $expressionFactory;
        $this->compiler = $compiler;
    }

    public function parse(string $rule): string
    {
        $values = new SplStack();
        $this->compiler->clear();
        $this->operator = null;

        foreach ($this->ast->getStream($rule) as $token) {
            switch ($token->getType()) {
                case TokenType::VALUE:
                    $values->push($token->getValue());
                    break;
                case TokenType::LOGICAL:
                    $this->compiler->addLogical($token);
                    continue 2;
                case TokenType::PARENTHESIS:
                    $this->compiler->addParentheses($token);
                    continue 2;
                case TokenType::OPERATOR:
                    $this->assignOperator($token, $values);
                    continue 2;
                case TokenType::COMMENT:
                case TokenType::SPACE:
                    continue 2;
                default:
                    throw Exceptions\ParserException::unknownToken($token);
            }

            $this->evaluateExpression($values);
        }

        return $this->compiler->getCompiledRule();
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

    private function evaluateExpression(SplStack $values)
    {
        if (!isset($this->operator) || $values->count() !== 2) {
            return;
        }

        list ($rightValue, $leftValue) = $values;

        try {
            $expression = $this->expressionFactory->createFromOperator($this->operator);

            $this->compiler->addBoolean(
                $expression->evaluate($leftValue, $rightValue)
            );
        } catch (Exceptions\MissingOperatorException $e) {
            throw new Exceptions\ParserException('Missing operator');
        }

        while (!$values->isEmpty()) {
            $values->pop();
        }

        unset($this->operator);
    }
}
