<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\AST\TokenCollection;

final class EqualStrictExpression extends BaseExpression
{
    public function evaluate($leftValue, $rightValue) : bool
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
