<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Expression;

use nicoSWD\Rule\TokenStream\TokenCollection;

final class EqualStrictExpression extends BaseExpression
{
    public function evaluate(mixed $leftValue, mixed $rightValue): bool
    {
        if ($leftValue instanceof TokenCollection) {
            $leftValue = $leftValue->toArray();
        }

        if ($rightValue instanceof TokenCollection) {
            $rightValue = $rightValue->toArray();
        }

        return $leftValue === $rightValue;
    }
}
