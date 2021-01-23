<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

final class LessThanExpression extends BaseExpression
{
    public function evaluate(mixed $leftValue, mixed $rightValue): bool
    {
        return $leftValue < $rightValue;
    }
}
