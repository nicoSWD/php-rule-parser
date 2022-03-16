<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Evaluator;

enum Operator: string
{
    case LOGICAL_AND = '&';
    case LOGICAL_OR = '|';
}
