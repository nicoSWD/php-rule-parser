<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\Exceptions\ParserException;

/**
 * Class InExpression
 * @package nicoSWD\Rules\Expressions
 */
final class InExpression extends BaseExpression
{
    /**
     * @param string $leftValue
     * @param array  $rightValue
     * @return bool
     * @throws ParserException
     */
    public function evaluate($leftValue, $rightValue)
    {
        if (!is_array($rightValue)) {
            throw new ParserException(sprintf(
                'Expected array, got "%s" : "%s"',
                gettype($rightValue),
                $rightValue
            ));
        }

        return in_array($leftValue, $rightValue, \true);
    }
}
