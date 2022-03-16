<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript;

use nicoSWD\Rule\Grammar\Definition;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\Grammar\InternalFunction;
use nicoSWD\Rule\Grammar\InternalMethod;
use nicoSWD\Rule\TokenStream\Token\Token;

final class JavaScript extends Grammar
{
    public function getDefinition(): array
    {
        return [
            new Definition(Token::AND, '&&', 145),
            new Definition(Token::OR, '\|\|', 140),
            new Definition(Token::NOT_EQUAL_STRICT, '!==', 135),
            new Definition(Token::NOT_EQUAL, '<>|!=', 130),
            new Definition(Token::EQUAL_STRICT, '===', 125),
            new Definition(Token::EQUAL, '==', 120),
            new Definition(Token::IN, '\bin\b', 115),
            new Definition(Token::NOT_IN, '\bnot\s+in\b', 116),
            new Definition(Token::BOOL_TRUE, '\btrue\b', 110),
            new Definition(Token::BOOL_FALSE, '\bfalse\b', 111),
            new Definition(Token::NULL, '\bnull\b', 105),
            new Definition(Token::METHOD, '\.\s*[a-zA-Z_]\w*\s*\(', 100),
            new Definition(Token::FUNCTION, '[a-zA-Z_]\w*\s*\(', 95),
            new Definition(Token::FLOAT, '-?\d+(?:\.\d+)', 90),
            new Definition(Token::INTEGER, '-?\d+', 85),
            new Definition(Token::ENCAPSED_STRING, '"[^"]*"|\'[^\']*\'', 80),
            new Definition(Token::SMALLER_EQUAL, '<=', 75),
            new Definition(Token::GREATER_EQUAL, '>=', 70),
            new Definition(Token::SMALLER, '<', 65),
            new Definition(Token::GREATER, '>', 60),
            new Definition(Token::OPENING_PARENTHESIS, '\(', 55),
            new Definition(Token::CLOSING_PARENTHESIS, '\)', 50),
            new Definition(Token::OPENING_ARRAY, '\[', 45),
            new Definition(Token::CLOSING_ARRAY, '\]', 40),
            new Definition(Token::COMMA, ',', 35),
            new Definition(Token::REGEX, '/[^/\*].*/[igm]{0,3}', 30),
            new Definition(Token::COMMENT, '//[^\r\n]*|/\*.*?\*/', 25),
            new Definition(Token::NEWLINE, '\r?\n', 20),
            new Definition(Token::SPACE, '\s+', 15),
            new Definition(Token::VARIABLE, '[a-zA-Z_]\w*', 10),
            new Definition(Token::UNKNOWN, '.', 5),
        ];
    }

    public function getInternalFunctions(): array
    {
        return [
            new InternalFunction('parseInt', Functions\ParseInt::class),
            new InternalFunction('parseFloat', Functions\ParseFloat::class),
        ];
    }

    public function getInternalMethods(): array
    {
        return [
            new InternalMethod('charAt', Methods\CharAt::class),
            new InternalMethod('concat', Methods\Concat::class),
            new InternalMethod('indexOf', Methods\IndexOf::class),
            new InternalMethod('join', Methods\Join::class),
            new InternalMethod('replace', Methods\Replace::class),
            new InternalMethod('split', Methods\Split::class),
            new InternalMethod('substr', Methods\Substr::class),
            new InternalMethod('test', Methods\Test::class),
            new InternalMethod('toLowerCase', Methods\ToLowerCase::class),
            new InternalMethod('toUpperCase', Methods\ToUpperCase::class),
            new InternalMethod('startsWith', Methods\StartsWith::class),
            new InternalMethod('endsWith', Methods\EndsWith::class),
        ];
    }
}
