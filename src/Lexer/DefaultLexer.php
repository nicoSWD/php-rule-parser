<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Lexer;

use Iterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

/**
 * A character-by-character lexer that replaces the regex-based Tokenizer.
 *
 * This approach provides:
 * - Linear O(n) performance with no regex backtracking
 * - Better error messages with precise character positions
 * - Context-sensitive lexing (e.g., distinguishing /regex/ from comments)
 * - Easier to extend with new syntax
 *
 * The Lexer is stateless and reentrant: all mutable scanning state is held
 * in a LexerContext object that is local to each tokenize() call.
 */
final class DefaultLexer extends Lexer
{
    public function __construct(
        public Grammar $grammar,
        private readonly TokenFactory $tokenFactory,
    ) {
    }

    public function tokenize(string $string): Iterator
    {
        $ctx = new LexerContext($string);
        $lastValueToken = null;

        while ($ctx->isValid()) {
            $ch = $ctx->current();

            $token = match (true) {
                $ch === '&' && $ctx->peek() === '&' => $this->emitOperator($ctx, TokenKind::AND, '&&', 2),
                $ch === '|' && $ctx->peek() === '|' => $this->emitOperator($ctx, TokenKind::OR, '||', 2),
                $ch === '!' && $ctx->peek() === '=' && $ctx->peek(1) === '=' => $this->emitOperator($ctx, TokenKind::NOT_EQUAL_STRICT, '!==', 3),
                $ch === '!' && $ctx->peek() === '=' => $this->emitOperator($ctx, TokenKind::NOT_EQUAL, '!=', 2),
                $ch === '!' => $this->emitOperator($ctx, TokenKind::NOT, '!', 1),
                $ch === '=' && $ctx->peek() === '=' && $ctx->peek(1) === '=' => $this->emitOperator($ctx, TokenKind::EQUAL_STRICT, '===', 3),
                $ch === '=' && $ctx->peek() === '=' => $this->emitOperator($ctx, TokenKind::EQUAL, '==', 2),
                $ch === '<' && $ctx->peek() === '=' => $this->emitOperator($ctx, TokenKind::LESS_THAN_EQUAL, '<=', 2),
                $ch === '>' && $ctx->peek() === '=' => $this->emitOperator($ctx, TokenKind::GREATER_EQUAL, '>=', 2),
                $ch === '+' => $this->emitOperator($ctx, TokenKind::PLUS, '+', 1),
                $ch === '-' && $this->isTokenValue($lastValueToken) => $this->emitOperator($ctx, TokenKind::MINUS, '-', 1),
                $ch === '-' && $this->isDigit($ctx->peek()) => $this->readNumber($ctx),
                $ch === '-' => $this->emitOperator($ctx, TokenKind::MINUS, '-', 1),
                $ch === '*' => $this->emitOperator($ctx, TokenKind::MULTIPLY, '*', 1),
                $ch === '/' && ($ctx->peek() === '/' || $ctx->peek() === '*') => $this->readSlash($ctx),
                $ch === '/' && $this->isTokenValue($lastValueToken) => $this->emitOperator($ctx, TokenKind::DIVIDE, '/', 1),
                $ch === '/' => $this->readSlash($ctx),
                $ch === '%' => $this->emitOperator($ctx, TokenKind::MODULO, '%', 1),
                $ch === '<' && $ctx->peek() === '>' => $this->emitOperator($ctx, TokenKind::NOT_EQUAL, '<>', 2),
                $ch === '<' => $this->emitOperator($ctx, TokenKind::LESS_THAN, '<', 1),
                $ch === '>' => $this->emitOperator($ctx, TokenKind::GREATER, '>', 1),
                $ch === '(' => $this->emitSimple($ctx, TokenKind::OPENING_PARENTHESIS, '('),
                $ch === ')' => $this->emitSimple($ctx, TokenKind::CLOSING_PARENTHESIS, ')'),
                $ch === '[' => $this->emitSimple($ctx, TokenKind::OPENING_ARRAY, '['),
                $ch === ']' => $this->emitSimple($ctx, TokenKind::CLOSING_ARRAY, ']'),
                $ch === ',' => $this->emitSimple($ctx, TokenKind::COMMA, ','),
                $ch === '.' => $this->readMethod($ctx),
                $ch === '"' || $ch === "'" => $this->readString($ctx),
                $this->isDigit($ch) => $this->readNumber($ctx),
                $ch === ' ' || $ch === "\t" => $this->readWhitespace($ctx),
                $ch === "\r" || $ch === "\n" => $this->readNewlineToken($ctx),
                $this->isAlpha($ch) || $ch === '_' => $this->readIdentifier($ctx),
                default => $this->emitSimple($ctx, TokenKind::UNKNOWN, $ch),
            };

            // Track the last non-ignorable token for context-sensitive lexing
            if (!$token->canBeIgnored()) {
                $lastValueToken = $token;
            }

            yield $token;
        }
    }

    /**
     * Determine if the given token represents a "value" — meaning the next
     * operator-like character should be treated as a binary operator rather
     * than a unary prefix.
     */
    private function isTokenValue(?BaseToken $token): bool
    {
        if ($token === null) {
            return false;
        }

        // A token is a "value" if it's a value-type token (string, number, bool, etc.),
        // or if it's a closing parenthesis/array (which represent
        // the end of a sub-expression that evaluates to a value).
        return match ($token->getKind()) {
            TokenKind::STRING,
            TokenKind::INTEGER,
            TokenKind::FLOAT,
            TokenKind::BOOL_TRUE,
            TokenKind::BOOL_FALSE,
            TokenKind::NULL,
            TokenKind::REGEX,
            TokenKind::VARIABLE,
            TokenKind::ENCAPSED_STRING,
            TokenKind::OBJECT,
            TokenKind::ARRAY,
            TokenKind::CLOSING_PARENTHESIS,
            TokenKind::CLOSING_ARRAY => true,
            default => false,
        };
    }

    private function emitSimple(LexerContext $ctx, TokenKind $token, string $value): BaseToken
    {
        $offset = $ctx->pos;
        $ctx->pos += strlen($value);

        return $this->tokenFactory->createFromToken($token, [$token->value => $value], $offset);
    }

    private function emitOperator(LexerContext $ctx, TokenKind $token, string $value, int $length): BaseToken
    {
        $offset = $ctx->pos;
        $ctx->pos += $length;

        return $this->tokenFactory->createFromToken($token, [$token->value => $value], $offset);
    }

    private function readMethod(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;
        $ctx->pos++; // skip '.'

        // Skip all whitespace (including newlines) between dot and method name
        $this->skipAllWhitespace($ctx);

        // Read method name
        $name = '';
        while ($ctx->isValid() && ($this->isAlpha($ctx->current()) || $ctx->current() === '_' || $this->isDigit($ctx->current()))) {
            $name .= $ctx->current();
            $ctx->pos++;
        }

        return $this->tokenFactory->createFromToken(TokenKind::METHOD, [TokenKind::METHOD->value => $name], $offset);
    }

    private function readString(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;
        $quote = $ctx->current();
        $ctx->pos++; // skip opening quote

        $value = $quote . $this->readDelimitedContent($ctx, $quote);

        return $this->tokenFactory->createFromToken(TokenKind::ENCAPSED_STRING, [TokenKind::ENCAPSED_STRING->value => $value], $offset);
    }

    /**
     * Read content delimited by a character, handling backslash escapes.
     *
     * The opening delimiter must already be consumed before calling this method.
     *
     * @param LexerContext $ctx The lexer context.
     * @param string $delimiter The closing delimiter character to look for.
     * @param bool $disallowNewlines If true, newlines will stop reading (for regex).
     * @return string The content between (but not including) the delimiters.
     */
    private function readDelimitedContent(LexerContext $ctx, string $delimiter, bool $disallowNewlines = false): string
    {
        $value = '';

        while ($ctx->isValid()) {
            $ch = $ctx->current();

            if ($ch === '\\') {
                $value .= $ch;
                $ctx->pos++;
                if ($ctx->isValid()) {
                    $value .= $ctx->current();
                    $ctx->pos++;
                }
                continue;
            }

            if ($ch === $delimiter) {
                $value .= $ch;
                $ctx->pos++;
                break;
            }

            if ($disallowNewlines && ($ch === "\r" || $ch === "\n")) {
                break;
            }

            $value .= $ch;
            $ctx->pos++;
        }

        return $value;
    }

    private function readSlash(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;

        // Single-line comment
        if ($ctx->peek() === '/') {
            $value = '//';
            $ctx->pos += 2;

            while ($ctx->isValid() && $ctx->current() !== "\r" && $ctx->current() !== "\n") {
                $value .= $ctx->current();
                $ctx->pos++;
            }

            return $this->tokenFactory->createFromToken(TokenKind::COMMENT, [TokenKind::COMMENT->value => $value], $offset);
        }

        // Multi-line comment
        if ($ctx->peek() === '*') {
            $value = '/*';
            $ctx->pos += 2;

            while ($ctx->isValid()) {
                if ($ctx->current() === '*' && $ctx->peek() === '/') {
                    $value .= '*/';
                    $ctx->pos += 2;
                    break;
                }
                $value .= $ctx->current();
                $ctx->pos++;
            }

            return $this->tokenFactory->createFromToken(TokenKind::COMMENT, [TokenKind::COMMENT->value => $value], $offset);
        }

        // Regex literal
        $ctx->pos++; // skip opening '/'
        $value = '/' . $this->readDelimitedContent($ctx, '/', disallowNewlines: true);

        // Read optional flags
        $seenFlags = [];
        while ($ctx->isValid() && str_contains('igm', $ctx->current())) {
            $flag = $ctx->current();

            if (isset($seenFlags[$flag])) {
                throw ParserException::duplicateRegexModifier($flag, $offset);
            }

            $seenFlags[$flag] = true;
            $value .= $flag;
            $ctx->pos++;
        }

        return $this->tokenFactory->createFromToken(TokenKind::REGEX, [TokenKind::REGEX->value => $value], $offset);
    }

    private function readNumber(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;
        $value = '';

        // Optional leading minus
        if ($ctx->current() === '-') {
            $value .= '-';
            $ctx->pos++;
        }

        // Integer part
        while ($ctx->isValid() && $this->isDigit($ctx->current())) {
            $value .= $ctx->current();
            $ctx->pos++;
        }

        // Float part
        if ($ctx->isValid() && $ctx->current() === '.' && $this->isDigit($ctx->peek())) {
            $value .= '.';
            $ctx->pos++;

            while ($ctx->isValid() && $this->isDigit($ctx->current())) {
                $value .= $ctx->current();
                $ctx->pos++;
            }

            return $this->tokenFactory->createFromToken(TokenKind::FLOAT, [TokenKind::FLOAT->value => $value], $offset);
        }

        return $this->tokenFactory->createFromToken(TokenKind::INTEGER, [TokenKind::INTEGER->value => $value], $offset);
    }

    private function readWhitespace(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;
        $value = '';

        while ($ctx->isValid() && ($ctx->current() === ' ' || $ctx->current() === "\t")) {
            $value .= $ctx->current();
            $ctx->pos++;
        }

        return $this->tokenFactory->createFromToken(TokenKind::SPACE, [TokenKind::SPACE->value => $value], $offset);
    }

    private function readNewlineToken(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;
        $ch = $ctx->current();
        $ctx->pos++;

        // Match \r optionally followed by \n (like old regex: \r?\n)
        if ($ch === "\r" && $ctx->isValid() && $ctx->current() === "\n") {
            $ctx->pos++;

            return $this->tokenFactory->createFromToken(TokenKind::NEWLINE, [TokenKind::NEWLINE->value => "\r\n"], $offset);
        }

        return $this->tokenFactory->createFromToken(TokenKind::NEWLINE, [TokenKind::NEWLINE->value => $ch], $offset);
    }

    private function readIdentifier(LexerContext $ctx): BaseToken
    {
        $offset = $ctx->pos;
        $name = '';

        while ($ctx->isValid() && ($this->isAlpha($ctx->current()) || $ctx->current() === '_' || $this->isDigit($ctx->current()))) {
            $name .= $ctx->current();
            $ctx->pos++;
        }

        // Check for "not in" keyword (must be checked before other keywords)
        if ($name === 'not') {
            $savedPos = $ctx->pos;
            $this->skipAllWhitespace($ctx);

            if ($ctx->startsWith('in')) {
                $ctx->pos += 2; // skip 'in'

                return $this->tokenFactory->createFromToken(TokenKind::NOT_IN, [TokenKind::NOT_IN->value => 'not in'], $offset);
            }

            $ctx->pos = $savedPos;
        }

        $token = match ($name) {
            'true' => TokenKind::BOOL_TRUE,
            'false' => TokenKind::BOOL_FALSE,
            'null' => TokenKind::NULL,
            'in' => TokenKind::IN,
            default => null,
        };

        if ($token !== null) {
            return $this->tokenFactory->createFromToken($token, [$token->value => $name], $offset);
        }

        // Check if it's a function call (identifier followed by optional whitespace and '(')
        $savedPos = $ctx->pos;
        $this->skipWhitespace($ctx);

        if ($ctx->isValid() && $ctx->current() === '(') {
            return $this->tokenFactory->createFromToken(TokenKind::FUNCTION, [TokenKind::FUNCTION->value => $name], $offset);
        }

        $ctx->pos = $savedPos;

        // It's a variable
        return $this->tokenFactory->createFromToken(TokenKind::VARIABLE, [TokenKind::VARIABLE->value => $name], $offset);
    }

    private function skipWhitespace(LexerContext $ctx): void
    {
        while ($ctx->isValid() && ($ctx->current() === ' ' || $ctx->current() === "\t")) {
            $ctx->pos++;
        }
    }

    private function skipAllWhitespace(LexerContext $ctx): void
    {
        while ($ctx->isValid() && ($ctx->current() === ' ' || $ctx->current() === "\t" || $ctx->current() === "\r" || $ctx->current() === "\n")) {
            $ctx->pos++;
        }
    }

    private function isAlpha(string $ch): bool
    {
        return ctype_alpha($ch);
    }

    private function isDigit(string $ch): bool
    {
        return ctype_digit($ch);
    }
}
