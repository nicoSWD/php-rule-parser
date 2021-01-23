<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

class TokenType
{
    public const OPERATOR = 1;
    public const INT_VALUE = 2;
    public const VALUE = 4;
    public const LOGICAL = 8;
    public const VARIABLE = 16;
    public const COMMENT = 32;
    public const SPACE = 64;
    public const UNKNOWN = 128;
    public const PARENTHESIS = 256;
    public const SQUARE_BRACKET = 512;
    public const COMMA = 1024;
    public const METHOD = 2048;
    public const FUNCTION = 4098;
}
