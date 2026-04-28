<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

enum ComparisonOperator: string
{
    case EQUAL = '==';
    case EQUAL_STRICT = '===';
    case NOT_EQUAL = '!=';
    case NOT_EQUAL_STRICT = '!==';
    case LESS_THAN = '<';
    case GREATER_THAN = '>';
    case LESS_THAN_EQUAL = '<=';
    case GREATER_THAN_EQUAL = '>=';
    case IN = 'in';
    case NOT_IN = 'not in';
}
