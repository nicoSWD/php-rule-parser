<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\TokenArray;

/**
 * Class InExpression
 * @package nicoSWD\Rules\Expressions
 */
final class InExpression extends BaseExpression
{
    /**
     * @param string     $leftValue
     * @param TokenArray $rightValue
     * @return bool
     * @throws ParserException
     */
    public function evaluate($leftValue, $rightValue)
    {
        // todo : fix all the different kind of crap that can get here
        if (is_array($rightValue)) {
            $array = $rightValue;
        } elseif ($rightValue instanceof TokenCollection) {
            $array = $rightValue->toArray();
        } elseif (!$rightValue instanceof TokenArray) {
            throw new ParserException(sprintf(
                'Expected array, got "%s"',
                gettype($rightValue)
            ));
        } else {
            $array = $rightValue->getValue();
        }

        return in_array($leftValue, $array, \true);
    }
}
