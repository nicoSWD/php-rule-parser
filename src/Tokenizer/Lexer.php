<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Tokenizer;

use ArrayIterator;
use Iterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\Token;
use nicoSWD\Rule\TokenStream\Token\TokenClosingArray;
use nicoSWD\Rule\TokenStream\Token\TokenClosingParenthesis;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\Type\Value;

/**
 * A character-by-character lexer that replaces the regex-based Tokenizer.
 *
 * This approach provides:
 * - Linear O(n) performance with no regex backtracking
 * - Better error messages with precise character positions
 * - Context-sensitive lexing (e.g., distinguishing /regex/ from comments)
 * - Easier to extend with new syntax
 */
final class Lexer extends TokenizerInterface
{
    private string $input;
    private int $pos;
    private int $length;

    public function __construct(
        public Grammar $grammar,
        private readonly TokenFactory $tokenFactory,
    ) {
    }

    public function tokenize(string $string): Iterator
    {
        $this->input = $string;
        $this->pos = 0;
        $this->length = strlen($string);

        $stack = [];

        while ($this->pos < $this->length) {
            $ch = $this->input[$this->pos];

            $token = match (true) {
                $ch === '&' && $this->peek() === '&' => $this->emitOperator(Token::AND, '&&', 2),
                $ch === '|' && $this->peek() === '|' => $this->emitOperator(Token::OR, '||', 2),
                $ch === '!' && $this->peek() === '=' && $this->peek(1) === '=' => $this->emitOperator(Token::NOT_EQUAL_STRICT, '!==', 3),
                $ch === '!' && $this->peek() === '=' => $this->emitOperator(Token::NOT_EQUAL, '!=', 2),
                $ch === '!' => $this->emitOperator(Token::NOT, '!', 1),
                $ch === '=' && $this->peek() === '=' && $this->peek(1) === '=' => $this->emitOperator(Token::EQUAL_STRICT, '===', 3),
                $ch === '=' && $this->peek() === '=' => $this->emitOperator(Token::EQUAL, '==', 2),
                $ch === '<' && $this->peek() === '=' => $this->emitOperator(Token::LESS_THAN_EQUAL, '<=', 2),
                $ch === '>' && $this->peek() === '=' => $this->emitOperator(Token::GREATER_EQUAL, '>=', 2),
                $ch === '+' => $this->emitOperator(Token::PLUS, '+', 1),
                $ch === '-' && $this->lastTokenIsValue($stack) => $this->emitOperator(Token::MINUS, '-', 1),
                $ch === '-' && $this->isDigit($this->peek()) => $this->readNumber(),
                $ch === '-' => $this->emitOperator(Token::MINUS, '-', 1),
                $ch === '*' => $this->emitOperator(Token::MULTIPLY, '*', 1),
                $ch === '/' && ($this->peek() === '/' || $this->peek() === '*') => $this->readSlash(),
                $ch === '/' && $this->lastTokenIsValue($stack) => $this->emitOperator(Token::DIVIDE, '/', 1),
                $ch === '/' => $this->readSlash(),
                $ch === '%' => $this->emitOperator(Token::MODULO, '%', 1),
                $ch === '<' && $this->peek() === '>' => $this->emitOperator(Token::NOT_EQUAL, '<>', 2),
                $ch === '<' => $this->emitOperator(Token::LESS_THAN, '<', 1),
                $ch === '>' => $this->emitOperator(Token::GREATER, '>', 1),
                $ch === '(' => $this->emitSimple(Token::OPENING_PARENTHESIS, '('),
                $ch === ')' => $this->emitSimple(Token::CLOSING_PARENTHESIS, ')'),
                $ch === '[' => $this->emitSimple(Token::OPENING_ARRAY, '['),
                $ch === ']' => $this->emitSimple(Token::CLOSING_ARRAY, ']'),
                $ch === ',' => $this->emitSimple(Token::COMMA, ','),
                $ch === '.' => $this->readMethod(),
                $ch === '"' || $ch === "'" => $this->readString(),
                $this->isDigit($ch) => $this->readNumber(),
                $ch === ' ' || $ch === "\t" => $this->readWhitespace(),
                $ch === "\r" || $ch === "\n" => $this->readNewlineToken(),
                $this->isAlpha($ch) || $ch === '_' => $this->readIdentifier(),
                default => $this->emitSimple(Token::UNKNOWN, $ch),
            };

            $stack[] = $token;
        }

        return new ArrayIterator($stack);
    }

    /**
     * Determine if the last meaningful (non-whitespace, non-comment) token
     * was a "value" — meaning the next operator-like character should be
     * treated as a binary operator rather than a unary prefix.
     *
     * This replaces the fragile $afterValue boolean flag with a deterministic
     * check against the actual token type hierarchy.
     */
    private function lastTokenIsValue(array $stack): bool
    {
        // Walk backwards through the stack to find the last non-ignorable token
        for ($i = count($stack) - 1; $i >= 0; $i--) {
            $token = $stack[$i];

            if ($token->canBeIgnored()) {
                continue;
            }

            // A token is a "value" if it implements the Value interface,
            // or if it's a closing parenthesis/array (which represent
            // the end of a sub-expression that evaluates to a value).
            return $token instanceof Value
                || $token instanceof TokenClosingParenthesis
                || $token instanceof TokenClosingArray;
        }

        // Empty stack or only ignorable tokens means we're at the start
        // of an expression — not after a value.
        return false;
    }

    private function peek(int $offset = 0): string
    {
        $index = $this->pos + $offset + 1;

        return $index < $this->length ? $this->input[$index] : '';
    }

    private function emitSimple(Token $token, string $value): BaseToken
    {
        $offset = $this->pos;
        $this->pos += strlen($value);

        return $this->tokenFactory->createFromToken($token, [$token->value => $value], $offset);
    }

    private function emitOperator(Token $token, string $value, int $length): BaseToken
    {
        $offset = $this->pos;
        $this->pos += $length;

        return $this->tokenFactory->createFromToken($token, [$token->value => $value], $offset);
    }

    private function readMethod(): BaseToken
    {
        $offset = $this->pos;
        $this->pos++; // skip '.'

        // Skip all whitespace (including newlines) between dot and method name
        $this->skipAllWhitespace();

        // Read method name
        $name = '';
        while ($this->pos < $this->length && ($this->isAlpha($this->input[$this->pos]) || $this->input[$this->pos] === '_' || $this->isDigit($this->input[$this->pos]))) {
            $name .= $this->input[$this->pos];
            $this->pos++;
        }

        return $this->tokenFactory->createFromToken(Token::METHOD, [Token::METHOD->value => $name], $offset);
    }

    private function readString(): BaseToken
    {
        $offset = $this->pos;
        $quote = $this->input[$this->pos];
        $this->pos++; // skip opening quote

        $value = $quote . $this->readDelimitedContent($quote);

        return $this->tokenFactory->createFromToken(Token::ENCAPSED_STRING, [Token::ENCAPSED_STRING->value => $value], $offset);
    }

    /**
     * Read content delimited by a character, handling backslash escapes.
     *
     * The opening delimiter must already be consumed before calling this method.
     *
     * @param string $delimiter The closing delimiter character to look for.
     * @param bool $disallowNewlines If true, newlines will stop reading (for regex).
     * @return string The content between (but not including) the delimiters.
     */
    private function readDelimitedContent(string $delimiter, bool $disallowNewlines = false): string
    {
        $value = '';

        while ($this->pos < $this->length) {
            $ch = $this->input[$this->pos];

            if ($ch === '\\') {
                $value .= $ch;
                $this->pos++;
                if ($this->pos < $this->length) {
                    $value .= $this->input[$this->pos];
                    $this->pos++;
                }
                continue;
            }

            if ($ch === $delimiter) {
                $value .= $ch;
                $this->pos++;
                break;
            }

            if ($disallowNewlines && ($ch === "\r" || $ch === "\n")) {
                break;
            }

            $value .= $ch;
            $this->pos++;
        }

        return $value;
    }

    private function readSlash(): BaseToken
    {
        $offset = $this->pos;

        // Single-line comment
        if ($this->peek() === '/') {
            $value = '//';
            $this->pos += 2;

            while ($this->pos < $this->length && $this->input[$this->pos] !== "\r" && $this->input[$this->pos] !== "\n") {
                $value .= $this->input[$this->pos];
                $this->pos++;
            }

            return $this->tokenFactory->createFromToken(Token::COMMENT, [Token::COMMENT->value => $value], $offset);
        }

        // Multi-line comment
        if ($this->peek() === '*') {
            $value = '/*';
            $this->pos += 2;

            while ($this->pos < $this->length) {
                if ($this->input[$this->pos] === '*' && $this->peek() === '/') {
                    $value .= '*/';
                    $this->pos += 2;
                    break;
                }
                $value .= $this->input[$this->pos];
                $this->pos++;
            }

            return $this->tokenFactory->createFromToken(Token::COMMENT, [Token::COMMENT->value => $value], $offset);
        }

        // Regex literal
        $this->pos++; // skip opening '/'
        $value = '/' . $this->readDelimitedContent('/', disallowNewlines: true);

        // Read optional flags
        $seenFlags = [];
        while ($this->pos < $this->length && str_contains('igm', $this->input[$this->pos])) {
            $flag = $this->input[$this->pos];

            if (isset($seenFlags[$flag])) {
                throw ParserException::duplicateRegexModifier($flag, $offset);
            }

            $seenFlags[$flag] = true;
            $value .= $flag;
            $this->pos++;
        }

        return $this->tokenFactory->createFromToken(Token::REGEX, [Token::REGEX->value => $value], $offset);
    }

    private function readNumber(): BaseToken
    {
        $offset = $this->pos;
        $value = '';

        // Optional leading minus
        if ($this->input[$this->pos] === '-') {
            $value .= '-';
            $this->pos++;
        }

        // Integer part
        while ($this->pos < $this->length && $this->isDigit($this->input[$this->pos])) {
            $value .= $this->input[$this->pos];
            $this->pos++;
        }

        // Float part
        if ($this->pos < $this->length && $this->input[$this->pos] === '.' && $this->isDigit($this->peek())) {
            $value .= '.';
            $this->pos++;

            while ($this->pos < $this->length && $this->isDigit($this->input[$this->pos])) {
                $value .= $this->input[$this->pos];
                $this->pos++;
            }

            return $this->tokenFactory->createFromToken(Token::FLOAT, [Token::FLOAT->value => $value], $offset);
        }

        return $this->tokenFactory->createFromToken(Token::INTEGER, [Token::INTEGER->value => $value], $offset);
    }

    private function readWhitespace(): BaseToken
    {
        $offset = $this->pos;
        $value = '';

        while ($this->pos < $this->length && ($this->input[$this->pos] === ' ' || $this->input[$this->pos] === "\t")) {
            $value .= $this->input[$this->pos];
            $this->pos++;
        }

        return $this->tokenFactory->createFromToken(Token::SPACE, [Token::SPACE->value => $value], $offset);
    }

    private function readNewlineToken(): BaseToken
    {
        $offset = $this->pos;
        $ch = $this->input[$this->pos];
        $this->pos++;

        // Match \r optionally followed by \n (like old regex: \r?\n)
        if ($ch === "\r" && $this->pos < $this->length && $this->input[$this->pos] === "\n") {
            $this->pos++;

            return $this->tokenFactory->createFromToken(Token::NEWLINE, [Token::NEWLINE->value => "\r\n"], $offset);
        }

        return $this->tokenFactory->createFromToken(Token::NEWLINE, [Token::NEWLINE->value => $ch], $offset);
    }

    private function readIdentifier(): BaseToken
    {
        $offset = $this->pos;
        $name = '';

        while ($this->pos < $this->length && ($this->isAlpha($this->input[$this->pos]) || $this->input[$this->pos] === '_' || $this->isDigit($this->input[$this->pos]))) {
            $name .= $this->input[$this->pos];
            $this->pos++;
        }

        // Check for "not in" keyword (must be checked before other keywords)
        if ($name === 'not') {
            $savedPos = $this->pos;
            $this->skipAllWhitespace();

            if ($this->startsWith('in')) {
                $this->pos += 2; // skip 'in'
                return $this->tokenFactory->createFromToken(Token::NOT_IN, [Token::NOT_IN->value => 'not in'], $offset);
            }

            $this->pos = $savedPos;
        }

        $token = match ($name) {
            'true' => Token::BOOL_TRUE,
            'false' => Token::BOOL_FALSE,
            'null' => Token::NULL,
            'in' => Token::IN,
            default => null,
        };

        if ($token !== null) {
            return $this->tokenFactory->createFromToken($token, [$token->value => $name], $offset);
        }

        // Check if it's a function call (identifier followed by optional whitespace and '(')
        $savedPos = $this->pos;
        $this->skipWhitespace();

        if ($this->pos < $this->length && $this->input[$this->pos] === '(') {
            return $this->tokenFactory->createFromToken(Token::FUNCTION, [Token::FUNCTION->value => $name], $offset);
        }

        $this->pos = $savedPos;

        // It's a variable
        return $this->tokenFactory->createFromToken(Token::VARIABLE, [Token::VARIABLE->value => $name], $offset);
    }

    private function skipWhitespace(): void
    {
        while ($this->pos < $this->length && ($this->input[$this->pos] === ' ' || $this->input[$this->pos] === "\t")) {
            $this->pos++;
        }
    }

    private function skipAllWhitespace(): void
    {
        while ($this->pos < $this->length && ($this->input[$this->pos] === ' ' || $this->input[$this->pos] === "\t" || $this->input[$this->pos] === "\r" || $this->input[$this->pos] === "\n")) {
            $this->pos++;
        }
    }

    private function startsWith(string $needle): bool
    {
        $len = strlen($needle);

        if ($this->pos + $len > $this->length) {
            return false;
        }

        return substr($this->input, $this->pos, $len) === $needle;
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
