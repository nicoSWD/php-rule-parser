<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\AST\TokenCollection;

/**
 * Class EqualExpression
 * @package nicoSWD\Rules\Expressions
 */
final class EqualExpression extends BaseExpression
{
    /**
     * @param string $leftValue
     * @param string $rightValue
     * @return bool
     */
    public function evaluate($leftValue, $rightValue)
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

        return $leftItems == $rightItems;
    }
}
