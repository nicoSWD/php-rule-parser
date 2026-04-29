<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Parser;

use nicoSWD\Rule\AST\AdditionNode;
use nicoSWD\Rule\AST\ArrayNode;
use nicoSWD\Rule\AST\BoolNode;
use nicoSWD\Rule\AST\ComparisonNode;
use nicoSWD\Rule\AST\ComparisonOperator;
use nicoSWD\Rule\AST\DivisionNode;
use nicoSWD\Rule\AST\FloatNode;
use nicoSWD\Rule\AST\FunctionCallNode;
use nicoSWD\Rule\AST\IntegerNode;
use nicoSWD\Rule\AST\LogicalNode;
use nicoSWD\Rule\AST\LogicalOperator;
use nicoSWD\Rule\AST\MethodCallNode;
use nicoSWD\Rule\AST\ModuloNode;
use nicoSWD\Rule\AST\MultiplicationNode;
use nicoSWD\Rule\AST\Node;
use nicoSWD\Rule\AST\NotNode;
use nicoSWD\Rule\AST\NullNode;
use nicoSWD\Rule\AST\RegexNode;
use nicoSWD\Rule\AST\StringNode;
use nicoSWD\Rule\AST\SubtractionNode;
use nicoSWD\Rule\AST\UnaryMinusNode;
use nicoSWD\Rule\AST\VariableNode;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use nicoSWD\Rule\TokenStream\TokenIterator;
use nicoSWD\Rule\TokenStream\TokenStream;

/**
 * Recursive descent parser that builds an AST from the token stream.
 *
 * Grammar (precedence from lowest to highest):
 *   expression       -> logical_or
 *   logical_or       -> logical_and ( "||" logical_and )*
 *   logical_and      -> comparison ( "&&" comparison )*
 *   comparison       -> additive ( comparison_op additive )?
 *   additive         -> multiplicative ( ("+" | "-") multiplicative )*
 *   multiplicative   -> unary ( ("*" | "/" | "%") unary )*
 *   unary            -> "-" unary
 *                     | "!" unary
 *                     | primary
 *   primary          -> "(" expression ")"
 *                     | value
 *   value            -> variable method_call*
 *                     | function_call
 *                     | string | integer | float | bool | null | regex
 *                     | array_literal method_call*
 */
final readonly class Parser
{
    public function __construct(
        private TokenStream $tokenStream,
    ) {
    }

    /** @throws Exception\ParserException */
    public function parse(string $rule): Node
    {
        $tokenIterator = $this->tokenStream->getStream($rule);

        if (!$tokenIterator->valid()) {
            return new BoolNode(false);
        }

        $node = $this->parseExpression($tokenIterator);

        // If there are remaining non-ignorable tokens, that's a syntax error
        $this->skipIgnoredTokens($tokenIterator);
        if ($tokenIterator->valid()) {
            throw Exception\ParserException::unexpectedToken($tokenIterator->peekRaw());
        }

        return $node;
    }

    /** @throws Exception\ParserException */
    private function parseExpression(TokenIterator $tokens): Node
    {
        return $this->parseLogicalOr($tokens);
    }

    /** @throws Exception\ParserException */
    private function parseLogicalOr(TokenIterator $tokens): Node
    {
        $left = $this->parseLogicalAnd($tokens);

        while ($this->peekToken($tokens)?->isOfKind(TokenKind::OR)) {
            $this->consumeToken($tokens); // consume ||
            $right = $this->parseLogicalAnd($tokens);
            $left = new LogicalNode($left, $right, LogicalOperator::OR);
        }

        return $left;
    }

    /** @throws Exception\ParserException */
    private function parseLogicalAnd(TokenIterator $tokens): Node
    {
        $left = $this->parseComparison($tokens);

        while ($this->peekToken($tokens)?->isOfKind(TokenKind::AND)) {
            $this->consumeToken($tokens); // consume &&
            $right = $this->parseComparison($tokens);
            $left = new LogicalNode($left, $right, LogicalOperator::AND);
        }

        return $left;
    }

    /** @throws Exception\ParserException */
    private function parseComparison(TokenIterator $tokens): Node
    {
        $left = $this->parseAdditive($tokens);

        $operatorToken = $this->peekToken($tokens);

        if ($operatorToken !== null) {
            $operator = $this->matchComparisonOperator($operatorToken);

            if ($operator !== null) {
                $this->consumeToken($tokens); // consume operator
                $right = $this->parseAdditive($tokens);

                return new ComparisonNode($left, $right, $operator);
            }
        }

        return $left;
    }

    /** @throws Exception\ParserException */
    private function parseAdditive(TokenIterator $tokens): Node
    {
        $left = $this->parseMultiplicative($tokens);

        while (
            ($peeked = $this->peekToken($tokens)) !== null
            && ($peeked->isOfKind(TokenKind::PLUS) || $peeked->isOfKind(TokenKind::MINUS))
        ) {
            $operator = $this->peekToken($tokens);
            $this->consumeToken($tokens); // consume + or -
            $right = $this->parseMultiplicative($tokens);

            $left = match ($operator->getKind()) {
                TokenKind::PLUS => new AdditionNode($left, $right),
                TokenKind::MINUS => new SubtractionNode($left, $right),
                default => throw new \RuntimeException('Unexpected additive operator'),
            };
        }

        return $left;
    }

    /** @throws Exception\ParserException */
    private function parseMultiplicative(TokenIterator $tokens): Node
    {
        $left = $this->parseUnary($tokens);

        while (
            ($peeked = $this->peekToken($tokens)) !== null
            && ($peeked->isOfKind(TokenKind::MULTIPLY) || $peeked->isOfKind(TokenKind::DIVIDE) || $peeked->isOfKind(TokenKind::MODULO))
        ) {
            $operator = $this->peekToken($tokens);
            $this->consumeToken($tokens); // consume *, /, or %
            $right = $this->parseUnary($tokens);

            $left = match ($operator->getKind()) {
                TokenKind::MULTIPLY => new MultiplicationNode($left, $right),
                TokenKind::DIVIDE => new DivisionNode($left, $right),
                TokenKind::MODULO => new ModuloNode($left, $right),
                default => throw new \RuntimeException('Unexpected multiplicative operator'),
            };
        }

        return $left;
    }

    /** @throws Exception\ParserException */
    private function parseUnary(TokenIterator $tokens): Node
    {
        $this->skipIgnoredTokens($tokens);

        if (!$tokens->valid()) {
            throw Exception\ParserException::unexpectedEndOfString();
        }

        $token = $tokens->peekRaw();

        // Unary minus: -expr
        if ($token->isOfKind(TokenKind::MINUS)) {
            $tokens->next();
            $operand = $this->parseUnary($tokens);

            return new UnaryMinusNode($operand);
        }

        // Logical NOT: !expr
        if ($token->isOfKind(TokenKind::NOT)) {
            $tokens->next();
            $operand = $this->parseUnary($tokens);

            return new NotNode($operand);
        }

        return $this->parsePrimary($tokens);
    }

    /** @throws Exception\ParserException */
    private function parsePrimary(TokenIterator $tokens): Node
    {
        $this->skipIgnoredTokens($tokens);

        if (!$tokens->valid()) {
            throw Exception\ParserException::unexpectedEndOfString();
        }

        $token = $tokens->peekRaw();

        // Parenthesized expression
        if ($token->isOfKind(TokenKind::OPENING_PARENTHESIS)) {
            $tokens->next();
            $node = $this->parseExpression($tokens);
            $this->expectClosingParenthesis($tokens);

            return $node;
        }

        // Array literal (may have method calls chained)
        if ($token->isOfKind(TokenKind::OPENING_ARRAY)) {
            $node = $this->parseArrayLiteral($tokens);
            return $this->parseMethodChain($node, $tokens);
        }

        // Function call
        if ($token->isOfKind(TokenKind::FUNCTION)) {
            $node = $this->parseFunctionCall($tokens);
            return $this->parseMethodChain($node, $tokens);
        }

        // Simple value tokens (advance past them)
        $node = $this->parseSimpleValue($token);

        if ($node !== null) {
            $tokens->next();

            // Check for method calls chained onto this value
            return $this->parseMethodChain($node, $tokens);
        }

        throw Exception\ParserException::unexpectedToken($token);
    }

    private function parseSimpleValue(BaseToken $token): ?Node
    {
        return match ($token->getKind()) {
            TokenKind::VARIABLE => new VariableNode($token->getOriginalValue(), $token->getOffset()),
            TokenKind::ENCAPSED_STRING, TokenKind::STRING => new StringNode($token->getValue()),
            TokenKind::INTEGER => new IntegerNode($token->getValue()),
            TokenKind::FLOAT => new FloatNode($token->getValue()),
            TokenKind::BOOL_TRUE => new BoolNode(true),
            TokenKind::BOOL_FALSE => new BoolNode(false),
            TokenKind::NULL => new NullNode(),
            TokenKind::REGEX => new RegexNode($token->getValue(), $token),
            default => null,
        };
    }

    /** @throws Exception\ParserException */
    private function parseFunctionCall(TokenIterator $tokens): FunctionCallNode
    {
        $token = $tokens->peekRaw();
        $functionName = $token->getValue();
        $offset = $token->getOffset();

        // Move past the function token
        $tokens->next();

        $arguments = $this->parseParenthesizedArguments($tokens);

        return new FunctionCallNode($functionName, $arguments, $offset);
    }

    /** @throws Exception\ParserException */
    private function parseMethodChain(Node $object, TokenIterator $tokens): Node
    {
        $this->skipIgnoredTokens($tokens);

        while ($tokens->valid() && $tokens->peekRaw()->isOfKind(TokenKind::METHOD)) {
            $methodToken = $tokens->peekRaw();
            $methodName = $methodToken->getValue();
            $offset = $methodToken->getOffset();
            $tokens->next();

            $arguments = $this->parseParenthesizedArguments($tokens);

            $object = new MethodCallNode($object, $methodName, $arguments, $offset);

            $this->skipIgnoredTokens($tokens);
        }

        return $object;
    }

    /** @throws Exception\ParserException */
    private function parseParenthesizedArguments(TokenIterator $tokens): array
    {
        // Consume the opening parenthesis
        $this->skipIgnoredTokens($tokens);
        if (!$tokens->valid() || !$tokens->peekRaw()->isOfKind(TokenKind::OPENING_PARENTHESIS)) {
            throw Exception\ParserException::unexpectedToken($tokens->valid() ? $tokens->peekRaw() : null);
        }
        $tokens->next();

        return $this->parseArguments($tokens);
    }

    /** @throws Exception\ParserException */
    private function parseArguments(TokenIterator $tokens): array
    {
        return $this->parseCommaSeparatedList(
            $tokens,
            static fn (BaseToken $token): bool => $token->isOfKind(TokenKind::CLOSING_PARENTHESIS),
        );
    }

    /** @throws Exception\ParserException */
    private function parseArrayLiteral(TokenIterator $tokens): ArrayNode
    {
        // Consume the opening '['
        $tokens->next();

        $items = $this->parseCommaSeparatedList(
            $tokens,
            static fn (BaseToken $token): bool => $token->isOfKind(TokenKind::CLOSING_ARRAY),
        );

        return new ArrayNode($items);
    }

    /**
     * @param callable(BaseToken): bool $isTerminator
     * @return Node[]
     * @throws Exception\ParserException
     */
    private function parseCommaSeparatedList(TokenIterator $tokens, callable $isTerminator): array
    {
        $items = [];
        $expectComma = false;

        while ($tokens->valid()) {
            $this->skipIgnoredTokens($tokens);

            if (!$tokens->valid()) {
                throw Exception\ParserException::unexpectedEndOfString();
            }

            $token = $tokens->peekRaw();

            // Closing token ends the list
            if ($isTerminator($token)) {
                $tokens->next(); // consume the closing token
                return $items;
            }

            if ($token->isOfKind(TokenKind::COMMA)) {
                if (!$expectComma) {
                    throw Exception\ParserException::unexpectedComma($token);
                }
                $expectComma = false;
                $tokens->next();
                continue;
            }

            if ($expectComma) {
                throw Exception\ParserException::unexpectedToken($token);
            }

            // Parse the item value (could be a complex expression)
            $item = $this->parsePrimary($tokens);
            $items[] = $item;
            $expectComma = true;
        }

        throw Exception\ParserException::unexpectedEndOfString();
    }

    /** @throws Exception\ParserException */
    private function expectClosingParenthesis(TokenIterator $tokens): void
    {
        $this->skipIgnoredTokens($tokens);

        if (!$tokens->valid()) {
            throw Exception\ParserException::unexpectedEndOfString();
        }

        $token = $tokens->peekRaw();

        if (!$token->isOfKind(TokenKind::CLOSING_PARENTHESIS)) {
            throw Exception\ParserException::unexpectedToken($token);
        }

        $tokens->next(); // consume ')'
    }

    private function matchComparisonOperator(BaseToken $token): ?ComparisonOperator
    {
        return match ($token->getKind()) {
            TokenKind::EQUAL => ComparisonOperator::EQUAL,
            TokenKind::EQUAL_STRICT => ComparisonOperator::EQUAL_STRICT,
            TokenKind::NOT_EQUAL => ComparisonOperator::NOT_EQUAL,
            TokenKind::NOT_EQUAL_STRICT => ComparisonOperator::NOT_EQUAL_STRICT,
            TokenKind::LESS_THAN => ComparisonOperator::LESS_THAN,
            TokenKind::GREATER => ComparisonOperator::GREATER_THAN,
            TokenKind::LESS_THAN_EQUAL => ComparisonOperator::LESS_THAN_EQUAL,
            TokenKind::GREATER_EQUAL => ComparisonOperator::GREATER_THAN_EQUAL,
            TokenKind::IN => ComparisonOperator::IN,
            TokenKind::NOT_IN => ComparisonOperator::NOT_IN,
            default => null,
        };
    }

    private function peekToken(TokenIterator $tokens): ?BaseToken
    {
        $this->skipIgnoredTokens($tokens);

        return $tokens->valid() ? $tokens->peekRaw() : null;
    }

    private function consumeToken(TokenIterator $tokens): void
    {
        $tokens->next();
    }

    private function skipIgnoredTokens(TokenIterator $tokens): void
    {
        while ($tokens->valid() && $tokens->peekRaw()->canBeIgnored()) {
            $tokens->next();
        }
    }
}
