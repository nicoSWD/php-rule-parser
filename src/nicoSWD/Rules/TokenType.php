<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

class TokenType
{
    const VALUE = 1;
    const OPERATOR = 2;
    const LOGICAL = 3;
    const VARIABLE = 4;
    const COMMENT = 5;
    const SPACE = 6;
    const UNKNOWN = 7;
    const PARENTHESIS = 8;
    const SQUARE_BRACKETS = 9;
    const COMMA = 10;
    const METHOD = 11;
}
