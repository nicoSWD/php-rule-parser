<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\Parser\Exception\ParserException;

abstract class BaseExpression
{
    /** @throws ParserException */
    abstract public function evaluate(mixed $leftValue, mixed $rightValue): bool;
}
