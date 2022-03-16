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
use nicoSWD\Rule\TokenStream\TokenStream;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenType;
use nicoSWD\Rule\TokenStream\Token\Type\Operator;

final class Parser
{
    public function __construct(
        private readonly TokenStream $tokenStream,
        private readonly EvaluatableExpressionFactory $expressionFactory,
        private readonly CompilerFactoryInterface $compilerFactory,
    ) {
    }

    /** @throws Exception\ParserException */
    public function parse(string $rule): string
    {
        $compiler = $this->compilerFactory->create();
        $expression = $this->expressionFactory->create();

        foreach ($this->tokenStream->getStream($rule) as $token) {
            $handler = $this->getHandlerForToken($token, $expression);
            $handler($compiler);

            if ($expression->isComplete()) {
                $compiler->addBoolean($expression->evaluate());
            }
        }

        return $compiler->getCompiledRule();
    }

    private function getHandlerForToken(BaseToken $token, EvaluatableExpression $expression): Closure
    {
        return match ($token->getType()) {
            TokenType::VALUE => $this->handleValueToken($token, $expression),
            TokenType::OPERATOR => $this->handleOperatorToken($token, $expression),
            TokenType::LOGICAL => $this->handleLogicalToken($token),
            TokenType::PARENTHESIS => $this->handleParenthesisToken($token),
            TokenType::COMMENT, TokenType::SPACE => $this->handleDummyToken(),
            default => $this->handleUnknownToken($token),
        };
    }

    private function handleValueToken(BaseToken $token, EvaluatableExpression $expression): Closure
    {
        return static fn () => $expression->addValue($token->getValue());
    }

    private function handleLogicalToken(BaseToken $token): Closure
    {
        return static fn (CompilerInterface $compiler) => $compiler->addLogical($token);
    }

    private function handleParenthesisToken(BaseToken $token): Closure
    {
        return static fn (CompilerInterface $compiler) => $compiler->addParentheses($token);
    }

    private function handleUnknownToken(BaseToken $token): Closure
    {
        return static fn () => throw Exception\ParserException::unknownToken($token);
    }

    private function handleOperatorToken(BaseToken & Operator $token, EvaluatableExpression $expression): Closure
    {
        return static function () use ($token, $expression): void {
            if ($expression->hasOperator()) {
                throw Exception\ParserException::unexpectedToken($token);
            } elseif ($expression->hasNoValues()) {
                throw Exception\ParserException::incompleteExpression($token);
            }

            $expression->operator = $token;
        };
    }

    private function handleDummyToken(): Closure
    {
        return static function (): void {
            // Do nothing
        };
    }
}
