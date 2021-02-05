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
    private ?BaseToken $operator;
    private array $values = [];

    public function __construct(
        private AST $ast,
        private ExpressionFactoryInterface $expressionFactory,
        private CompilerFactoryInterface $compilerFactory
    ) {
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
        return match ($tokenType) {
            TokenType::VALUE, TokenType::INT_VALUE => $this->handleValueToken(),
            TokenType::OPERATOR => $this->handleOperatorToken(),
            TokenType::LOGICAL => $this->handleLogicalToken(),
            TokenType::PARENTHESIS => $this->handleParenthesisToken(),
            TokenType::COMMENT, TokenType::SPACE => $this->handleDummyToken(),
            default => $this->handleUnknownToken(),
        };
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
        return count($this->values) === 2;
    }

    private function handleValueToken(): Closure
    {
        return fn (BaseToken $token) => $this->values[] = $token->getValue();
    }

    private function handleLogicalToken(): Closure
    {
        return fn (BaseToken $token, CompilerInterface $compiler) => $compiler->addLogical($token);
    }

    private function handleParenthesisToken(): Closure
    {
        return fn (BaseToken $token, CompilerInterface $compiler) => $compiler->addParentheses($token);
    }

    private function handleUnknownToken(): Closure
    {
        return fn (BaseToken $token) => throw Exception\ParserException::unknownToken($token);
    }

    private function handleOperatorToken(): Closure
    {
        return function (BaseToken $token): void {
            if (isset($this->operator)) {
                throw Exception\ParserException::unexpectedToken($token);
            } elseif (empty($this->values)) {
                throw Exception\ParserException::incompleteExpression($token);
            }

            $this->operator = $token;
        };
    }

    private function handleDummyToken(): Closure
    {
        return function (): void {
            // Do nothing
        };
    }

    private function resetState(): void
    {
        $this->operator = null;
        $this->values = [];
    }
}
