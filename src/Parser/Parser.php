<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Parser;

use Closure;
use nicoSWD\Rule\Compiler\CompilerFactoryInterface;
use nicoSWD\Rule\Compiler\CompilerInterface;
use nicoSWD\Rule\Expression\ExpressionFactoryInterface;
use nicoSWD\Rule\TokenStream\AST;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenType;

class Parser
{
    /** @var AST */
    private $ast;
    /** @var ExpressionFactoryInterface */
    private $expressionFactory;
    /** @var CompilerFactoryInterface */
    private $compilerFactory;
    /** @var ?BaseToken */
    private $operator;
    /** @var mixed[] */
    private $values = [];
    /** @var Closure[] */
    private $handlers;

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
        $this->resetState();

        foreach ($this->ast->getStream($rule) as $token) {
            $handler = $this->getHandlerForType($token->getType());
            $handler($token, $compiler);

            if ($this->expressionCanBeEvaluated()) {
                $this->evaluateExpression($compiler);
            }
        }

        return $compiler->getCompiledRule();
    }

    private function getHandlerForType(int $tokenType): Closure
    {
        if (!isset($this->handlers)) {
            $this->handlers = [
                TokenType::VALUE       => $this->handleValueToken(),
                TokenType::INT_VALUE   => $this->handleValueToken(),
                TokenType::OPERATOR    => $this->handleOperatorToken(),
                TokenType::LOGICAL     => $this->handleLogicalToken(),
                TokenType::PARENTHESIS => $this->handleParenthesisToken(),
                TokenType::SPACE       => $this->handleDummyToken(),
                TokenType::COMMENT     => $this->handleDummyToken(),
                TokenType::UNKNOWN     => $this->handleUnknownToken(),
            ];
        }

        return $this->handlers[$tokenType] ?? $this->handlers[TokenType::UNKNOWN];
    }

    private function evaluateExpression(CompilerInterface $compiler): void
    {
        $expression = $this->expressionFactory->createFromOperator($this->operator);

        $compiler->addBoolean(
            $expression->evaluate(...$this->values)
        );

        $this->resetState();
    }

    private function expressionCanBeEvaluated(): bool
    {
        return isset($this->operator) && count($this->values) === 2;
    }

    private function handleValueToken(): Closure
    {
        return function (BaseToken $token) {
            $this->values[] = $token->getValue();
        };
    }

    private function handleOperatorToken(): Closure
    {
        return function (BaseToken $token) {
            if (isset($this->operator)) {
                throw Exception\ParserException::unexpectedToken($token);
            } elseif (empty($this->values)) {
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

    private function resetState(): void
    {
        $this->operator = null;
        $this->values = [];
    }
}
