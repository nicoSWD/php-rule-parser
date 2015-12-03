<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\AST\TokenCollection;

final class EqualStrictExpression extends BaseExpression
{
    public function evaluate($leftValue, $rightValue) : bool
    {
        if ($rightValue instanceof TokenCollection) {
            $rightItems = $rightValue->toArray();
        } else {
            $rightItems = $rightValue;
        }

        if ($leftValue instanceof TokenCollection) {
            $leftItems = $leftValue->toArray();
        } else {
            $leftItems = $leftValue;
        }

        return $leftItems === $rightItems;
    }
}
