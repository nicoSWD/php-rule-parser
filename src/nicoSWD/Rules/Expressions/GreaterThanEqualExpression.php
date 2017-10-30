<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Expressions;

final class GreaterThanEqualExpression extends BaseExpression
{
    public function evaluate($leftValue, $rightValue): bool
    {
        return $leftValue >= $rightValue;
    }
}
