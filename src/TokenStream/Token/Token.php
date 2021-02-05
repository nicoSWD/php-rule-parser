<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

class Token
{
    public const AND = 'And';
    public const OR = 'Or';
    public const NOT_EQUAL_STRICT = 'NotEqualStrict';
    public const NOT_EQUAL = 'NotEqual';
    public const EQUAL_STRICT = 'EqualStrict';
    public const EQUAL = 'Equal';
    public const IN = 'In';
    public const NOT_IN = 'NotIn';
    public const BOOL_TRUE = 'True';
    public const BOOL_FALSE = 'False';
    public const NULL = 'Null';
    public const METHOD = 'Method';
    public const FUNCTION = 'Function';
    public const VARIABLE = 'Variable';
    public const FLOAT = 'Float';
    public const INTEGER = 'Integer';
    public const ENCAPSED_STRING = 'EncapsedString';
    public const SMALLER_EQUAL = 'SmallerEqual';
    public const GREATER_EQUAL = 'GreaterEqual';
    public const SMALLER = 'Smaller';
    public const GREATER = 'Greater';
    public const OPENING_PARENTHESIS = 'OpeningParentheses';
    public const CLOSING_PARENTHESIS = 'ClosingParentheses';
    public const OPENING_ARRAY = 'OpeningArray';
    public const CLOSING_ARRAY = 'ClosingArray';
    public const COMMA = 'Comma';
    public const REGEX = 'Regex';
    public const COMMENT = 'Comment';
    public const NEWLINE = 'Newline';
    public const SPACE = 'Space';
    public const UNKNOWN = 'Unknown';
}
