<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules;

use SplPriorityQueue;
use stdClass;

final class Tokenizer implements TokenizerInterface
{
    const TOKEN_AND = 'And';
    const TOKEN_OR = 'Or';
    const TOKEN_NOT_EQUAL_STRICT = 'NotEqualStrict';
    const TOKEN_NOT_EQUAL = 'NotEqual';
    const TOKEN_EQUAL_STRICT = 'EqualStrict';
    const TOKEN_EQUAL = 'Equal';
    const TOKEN_IN = 'In';
    const TOKEN_BOOL = 'Bool';
    const TOKEN_NULL = 'Null';
    const TOKEN_METHOD = 'Method';
    const TOKEN_FUNCTION = 'Function';
    const TOKEN_VARIABLE = 'Variable';
    const TOKEN_FLOAT = 'Float';
    const TOKEN_INTEGER = 'Integer';
    const TOKEN_ENCAPSED_STRING = 'EncapsedString';
    const TOKEN_SMALLER_EQUAL = 'SmallerEqual';
    const TOKEN_GREATER_EQUAL = 'GreaterEqual';
    const TOKEN_SMALLER = 'Smaller';
    const TOKEN_GREATER = 'Greater';
    const TOKEN_OPENING_PARENTHESIS = 'OpeningParentheses';
    const TOKEN_CLOSING_PARENTHESIS = 'ClosingParentheses';
    const TOKEN_OPENING_ARRAY = 'OpeningArray';
    const TOKEN_CLOSING_ARRAY = 'ClosingArray';
    const TOKEN_COMMA = 'Comma';
    const TOKEN_REGEX = 'Regex';
    const TOKEN_COMMENT = 'Comment';
    const TOKEN_NEWLINE = 'Newline';
    const TOKEN_SPACE = 'Space';
    const TOKEN_UNKNOWN = 'Unknown';

    private $internalTokens = [];

    private $regex = '';

    private $regexRequiresReassembly = false;

    public function __construct()
    {
        $this->registerToken(self::TOKEN_AND, '&&', 145);
        $this->registerToken(self::TOKEN_OR, '\|\|', 140);
        $this->registerToken(self::TOKEN_NOT_EQUAL_STRICT, '!==', 135);
        $this->registerToken(self::TOKEN_NOT_EQUAL, '<>|!=', 130);
        $this->registerToken(self::TOKEN_EQUAL_STRICT, '===', 125);
        $this->registerToken(self::TOKEN_EQUAL, '==', 120);
        $this->registerToken(self::TOKEN_IN, '\bin\b', 115);
        $this->registerToken(self::TOKEN_BOOL, '\b(?:true|false)\b', 110);
        $this->registerToken(self::TOKEN_NULL, '\bnull\b', 105);
        $this->registerToken(self::TOKEN_METHOD, '\.\s*[a-zA-Z_]\w*\s*\(', 100);
        $this->registerToken(self::TOKEN_FUNCTION, '[a-zA-Z_]\w*\s*\(', 95);
        $this->registerToken(self::TOKEN_FLOAT, '-?\d+(?:\.\d+)', 90);
        $this->registerToken(self::TOKEN_INTEGER, '-?\d+', 85);
        $this->registerToken(self::TOKEN_ENCAPSED_STRING, '"[^"]*"|\'[^\']*\'', 80);
        $this->registerToken(self::TOKEN_SMALLER_EQUAL, '<=', 75);
        $this->registerToken(self::TOKEN_GREATER_EQUAL, '>=', 70);
        $this->registerToken(self::TOKEN_SMALLER, '<', 65);
        $this->registerToken(self::TOKEN_GREATER, '>', 60);
        $this->registerToken(self::TOKEN_OPENING_PARENTHESIS, '\(', 55);
        $this->registerToken(self::TOKEN_CLOSING_PARENTHESIS, '\)', 50);
        $this->registerToken(self::TOKEN_OPENING_ARRAY, '\[', 45);
        $this->registerToken(self::TOKEN_CLOSING_ARRAY, '\]', 40);
        $this->registerToken(self::TOKEN_COMMA, ',', 35);
        $this->registerToken(self::TOKEN_REGEX, '/[^/\*].*/[igm]{0,3}', 30);
        $this->registerToken(self::TOKEN_COMMENT, '//[^\r\n]*|/\*.*?\*/', 25);
        $this->registerToken(self::TOKEN_NEWLINE, '\r?\n', 20);
        $this->registerToken(self::TOKEN_SPACE, '\s+', 15);
        $this->registerToken(self::TOKEN_VARIABLE, '[a-zA-Z_]\w*', 10);
        $this->registerToken(self::TOKEN_UNKNOWN, '.', 5);
    }


    public function tokenize(string $string) : Stack
    {
        $stack = new Stack();
        $regex = $this->getRegex();
        $baseNameSpace = __NAMESPACE__ . '\\Tokens\\Token';
        $offset = 0;

        while (preg_match($regex, $string, $matches, 0, $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $baseNameSpace . $token;

            $stack->attach(new $className(
                $matches[$token],
                $offset,
                $stack
            ));

            $offset += strlen($matches[0]);
        }

        return $stack;
    }

    public function registerToken(string $class, string $regex, int $priority = null)
    {
        $token = new stdClass();
        $token->class = $class;
        $token->regex = $regex;
        $token->priority = $priority ?? $this->getPriority($class);

        $this->internalTokens[$class] = $token;
        $this->regexRequiresReassembly = true;
    }

    private function getMatchedToken(array $matches) : string
    {
        foreach ($matches as $key => $value) {
            if ($value !== '' && !is_int($key)) {
                return $key;
            }
        }

        return 'Unknown';
    }


    private function getRegex() : string
    {
        if (!$this->regex || $this->regexRequiresReassembly) {
            $regex = [];

            foreach ($this->getQueue() as $token) {
                $regex[] = "(?<$token->class>$token->regex)";
            }

            $this->regex = sprintf('~(%s)~As', implode('|', $regex));
            $this->regexRequiresReassembly = false;
        }

        return $this->regex;
    }


    private function getQueue() : SplPriorityQueue
    {
        $queue = new SplPriorityQueue();

        foreach ($this->internalTokens as $class) {
            $queue->insert($class, $class->priority);
        }

        return $queue;
    }

    private function getPriority(string $class) : int
    {
        return $this->internalTokens[$class]->priority ?? 10;
    }
}
