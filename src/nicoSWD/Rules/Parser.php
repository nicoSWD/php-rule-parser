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

    /** @var SplStack */
    private $values;

    public function __construct(AST $ast, ExpressionFactory $expressionFactory, Compiler $compiler)
    {
        $this->ast = $ast;
        $this->expressionFactory = $expressionFactory;
        $this->compiler = $compiler;
        $this->values = new SplStack();
    }

    public function parse(string $rule): string
    {
        $this->compiler->clear();
        $this->operator = null;

        foreach ($this->ast->getStream($rule) as $token) {
            switch ($token->getType()) {
                case TokenType::VALUE:
                    $this->assignVariableValueFromToken($token);
                    break;
                case TokenType::LOGICAL:
                    $this->compiler->addLogical($token);
                    continue 2;
                case TokenType::PARENTHESIS:
                    $this->compiler->addParentheses($token);
                    continue 2;
                case TokenType::OPERATOR:
                    $this->assignOperator($token);
                    continue 2;
                case TokenType::COMMENT:
                case TokenType::SPACE:
                    continue 2;
                default:
                    throw Exceptions\ParserException::unknownToken($token);
            }

            $this->evaluateExpression();
        }

        return $this->compiler->getCompiledRule();
    }

    private function assignVariableValueFromToken(BaseToken $token)
    {
        $this->compiler->flipOperatorRequired($token);
        $this->values->push($token->getValue());
    }

    private function assignOperator(BaseToken $token)
    {
        if (isset($this->operator)) {
            throw Exceptions\ParserException::unexpectedToken($token);
        } elseif ($this->values->isEmpty()) {
            throw Exceptions\ParserException::incompleteExpression($token);
        }

        $this->compiler->operatorRequired(false);
        $this->operator = $token;
    }

    private function evaluateExpression()
    {
        if (!isset($this->operator) || $this->values->count() !== 2) {
            return;
        }

        list ($rightValue, $leftValue) = $this->values;

        $this->compiler->addBoolean($this->getExpression()->evaluate($leftValue, $rightValue));
        $this->values->pop();
        $this->values->pop();

        unset($this->operator);
    }

    private function getExpression(): Expressions\BaseExpression
    {
        return $this->expressionFactory->createFromOperator($this->operator);
    }
}
