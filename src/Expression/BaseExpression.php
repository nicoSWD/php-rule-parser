<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

abstract class BaseExpression
{
    /**
     * @param mixed $leftValue
     * @param mixed $rightValue
     * @return bool
     */
    abstract public function evaluate($leftValue, $rightValue): bool;
}
