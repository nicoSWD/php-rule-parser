<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Parser;

use Closure;
use nicoSWD\Rule\Compiler\CompilerInterface;
use nicoSWD\Rule\Compiler\CompilerFactoryInterface;
use nicoSWD\Rule\Compiler\Exception\MissingOperatorException;
use nicoSWD\Rule\Expression\ExpressionFactoryInterface;
use nicoSWD\Rule\TokenStream\AST;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenType;
use SplStack;

class Parser
{
    /** @var AST */
    private $ast;
    /** @var ExpressionFactoryInterface */
    private $expressionFactory;
    /** @var CompilerFactoryInterface */
    private $compilerFactory;
    /** @var BaseToken|null */
    private $operator;
    /** @var SplStack */
    private $values;

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
        $this->values = new SplStack();

        foreach ($this->ast->getStream($rule) as $token) {
            $handler = $this->getTokenHandler($token);
            $handler($compiler);
        }

        return $compiler->getCompiledRule();
    }

    private function getTokenHandler(BaseToken $token): Closure
    {
        return function (CompilerInterface $compiler) use ($token) {
            $handlers = [
                TokenType::VALUE       => $this->handleValueToken(),
                TokenType::INT_VALUE   => $this->handleValueToken(),
                TokenType::OPERATOR    => $this->handleOperatorToken(),
                TokenType::LOGICAL     => $this->handleLogicalToken(),
                TokenType::PARENTHESIS => $this->handleParenthesisToken(),
                TokenType::SPACE       => $this->handleDummyToken(),
                TokenType::COMMENT     => $this->handleDummyToken(),
                TokenType::UNKNOWN     => $this->handleUnknownToken(),
            ];

            $handler = $handlers[$token->getType()] ?? $handlers[TokenType::UNKNOWN];
            $handler($token, $compiler);

            $this->evaluateExpression($compiler);
        };
    }

    private function evaluateExpression(CompilerInterface $compiler)
    {
        if (!isset($this->operator) || $this->values->count() !== 2) {
            return;
        }

        list($rightValue, $leftValue) = $this->values;

        try {
            $expression = $this->expressionFactory->createFromOperator($this->operator);

            $compiler->addBoolean(
                $expression->evaluate($leftValue, $rightValue)
            );
        } catch (MissingOperatorException $e) {
            throw new Exception\ParserException('Missing operator');
        }

        do {
            $this->values->pop();
        } while (!$this->values->isEmpty());

        unset($this->operator);
    }

    private function handleValueToken(): Closure
    {
        return function (BaseToken $token) {
            $this->values->push($token->getValue());
        };
    }

    private function handleOperatorToken(): Closure
    {
        return function (BaseToken $token) {
            if (isset($this->operator)) {
                throw Exception\ParserException::unexpectedToken($token);
            } elseif ($this->values->isEmpty()) {
                throw Exception\ParserException::incompleteExpression($token);
            }

            $this->operator = $token;
        };
    }

    private function handleLogicalToken(): Closure
    {
        return function (BaseToken $token, CompilerInterface $compiler) {
            $compiler->addLogical($token);
        };
    }

    private function handleParenthesisToken(): Closure
    {
        return function (BaseToken $token, CompilerInterface $compiler) {
            $compiler->addParentheses($token);
        };
    }

    private function handleUnknownToken(): Closure
    {
        return function (BaseToken $token) {
            throw Exception\ParserException::unknownToken($token);
        };
    }

    private function handleDummyToken(): Closure
    {
        return function () {
            // Do nothing
        };
    }
}
