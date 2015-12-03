<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Expressions;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Exceptions\ParserException;

final class InExpression extends BaseExpression
{
    /**
     * @throws ParserException
     */
    public function evaluate($leftValue, $rightValue) : bool
    {
        // Fix all the different kind of crap that can get here
        if (is_array($rightValue)) {
            $array = $rightValue;
        } elseif ($rightValue instanceof TokenCollection) {
            $array = $rightValue->toArray();
        } else {
            throw new ParserException(sprintf(
                'Expected array, got "%s"',
                gettype($rightValue)
            ));
        }

        return in_array($leftValue, $array, true);
    }
}
