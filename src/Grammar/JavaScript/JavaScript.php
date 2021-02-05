<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript;

use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\Token;

final class JavaScript extends Grammar
{
    public function getDefinition(): array
    {
        return [
            [Token::AND, '&&', 145],
            [Token::OR, '\|\|', 140],
            [Token::NOT_EQUAL_STRICT, '!==', 135],
            [Token::NOT_EQUAL, '<>|!=', 130],
            [Token::EQUAL_STRICT, '===', 125],
            [Token::EQUAL, '==', 120],
            [Token::IN, '\bin\b', 115],
            [Token::NOT_IN, '\bnot\s+in\b', 116],
            [Token::BOOL_TRUE, '\btrue\b', 110],
            [Token::BOOL_FALSE, '\bfalse\b', 111],
            [Token::NULL, '\bnull\b', 105],
            [Token::METHOD, '\.\s*[a-zA-Z_]\w*\s*\(', 100],
            [Token::FUNCTION, '[a-zA-Z_]\w*\s*\(', 95],
            [Token::FLOAT, '-?\d+(?:\.\d+)', 90],
            [Token::INTEGER, '-?\d+', 85],
            [Token::ENCAPSED_STRING, '"[^"]*"|\'[^\']*\'', 80],
            [Token::SMALLER_EQUAL, '<=', 75],
            [Token::GREATER_EQUAL, '>=', 70],
            [Token::SMALLER, '<', 65],
            [Token::GREATER, '>', 60],
            [Token::OPENING_PARENTHESIS, '\(', 55],
            [Token::CLOSING_PARENTHESIS, '\)', 50],
            [Token::OPENING_ARRAY, '\[', 45],
            [Token::CLOSING_ARRAY, '\]', 40],
            [Token::COMMA, ',', 35],
            [Token::REGEX, '/[^/\*].*/[igm]{0,3}', 30],
            [Token::COMMENT, '//[^\r\n]*|/\*.*?\*/', 25],
            [Token::NEWLINE, '\r?\n', 20],
            [Token::SPACE, '\s+', 15],
            [Token::VARIABLE, '[a-zA-Z_]\w*', 10],
            [Token::UNKNOWN, '.', 5],
        ];
    }

    public function getInternalFunctions(): array
    {
        return [
            'parseInt' => Functions\ParseInt::class,
            'parseFloat' => Functions\ParseFloat::class,
        ];
    }

    public function getInternalMethods(): array
    {
        return [
            'charAt' => Methods\CharAt::class,
            'concat' => Methods\Concat::class,
            'indexOf' => Methods\IndexOf::class,
            'join' => Methods\Join::class,
            'replace' => Methods\Replace::class,
            'split' => Methods\Split::class,
            'substr' => Methods\Substr::class,
            'test' => Methods\Test::class,
            'toLowerCase' => Methods\ToLowerCase::class,
            'toUpperCase' => Methods\ToUpperCase::class,
            'startsWith' => Methods\StartsWith::class,
            'endsWith' => Methods\EndsWith::class,
        ];
    }
}
