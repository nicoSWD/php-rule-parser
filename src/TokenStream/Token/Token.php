<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

class Token
{
    const AND = 'And';
    const OR = 'Or';
    const NOT_EQUAL_STRICT = 'NotEqualStrict';
    const NOT_EQUAL = 'NotEqual';
    const EQUAL_STRICT = 'EqualStrict';
    const EQUAL = 'Equal';
    const IN = 'In';
    const BOOL = 'Bool';
    const NULL = 'Null';
    const METHOD = 'Method';
    const FUNCTION = 'Function';
    const VARIABLE = 'Variable';
    const FLOAT = 'Float';
    const INTEGER = 'Integer';
    const ENCAPSED_STRING = 'EncapsedString';
    const SMALLER_EQUAL = 'SmallerEqual';
    const GREATER_EQUAL = 'GreaterEqual';
    const SMALLER = 'Smaller';
    const GREATER = 'Greater';
    const OPENING_PARENTHESIS = 'OpeningParentheses';
    const CLOSING_PARENTHESIS = 'ClosingParentheses';
    const OPENING_ARRAY = 'OpeningArray';
    const CLOSING_ARRAY = 'ClosingArray';
    const COMMA = 'Comma';
    const REGEX = 'Regex';
    const COMMENT = 'Comment';
    const NEWLINE = 'Newline';
    const SPACE = 'Space';
    const UNKNOWN = 'Unknown';
}
