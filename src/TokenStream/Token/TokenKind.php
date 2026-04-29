<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

/**
 * Identifies the specific kind of a token, replacing the need for
 * dozens of separate token classes.
 */
enum TokenKind: string
{
    case AND = 'And';
    case OR = 'Or';
    case NOT = 'Not';
    case NOT_EQUAL_STRICT = 'NotEqualStrict';
    case NOT_EQUAL = 'NotEqual';
    case EQUAL_STRICT = 'EqualStrict';
    case EQUAL = 'Equal';
    case IN = 'In';
    case NOT_IN = 'NotIn';
    case BOOL_TRUE = 'True';
    case BOOL_FALSE = 'False';
    case NULL = 'Null';
    case METHOD = 'Method';
    case FUNCTION = 'Function';
    case VARIABLE = 'Variable';
    case FLOAT = 'Float';
    case INTEGER = 'Integer';
    case ENCAPSED_STRING = 'EncapsedString';
    case LESS_THAN_EQUAL = 'SmallerEqual';
    case GREATER_EQUAL = 'GreaterEqual';
    case LESS_THAN = 'Smaller';
    case PLUS = 'Plus';
    case MINUS = 'Minus';
    case MULTIPLY = 'Multiply';
    case DIVIDE = 'Divide';
    case MODULO = 'Modulo';
    case GREATER = 'Greater';
    case OPENING_PARENTHESIS = 'OpeningParentheses';
    case CLOSING_PARENTHESIS = 'ClosingParentheses';
    case OPENING_ARRAY = 'OpeningArray';
    case CLOSING_ARRAY = 'ClosingArray';
    case COMMA = 'Comma';
    case REGEX = 'Regex';
    case COMMENT = 'Comment';
    case NEWLINE = 'Newline';
    case SPACE = 'Space';
    case UNKNOWN = 'Unknown';
    case STRING = 'String';
    case OBJECT = 'Object';
    case ARRAY = 'Array';
}
