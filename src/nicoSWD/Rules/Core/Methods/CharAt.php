<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\Core\CallableFunction;

/**
 * Class CharAt
 * @package nicoSWD\Rules\Core\Methods
 */
final class CharAt extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenString
     */
    public function call(TokenCollection $parameters)
    {
        $parameters->rewind();

        if ($parameters->count() < 1) {
            $offset = 0;
        } else {
            $offset = (int) $parameters->current()->getValue();
        }

        $tokenValue = $this->token->getValue();

        if (!isset($tokenValue[$offset])) {
            $char = '';
        } else {
            $char = $tokenValue[$offset];
        }

        return new TokenString(
            $char,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'charAt';
    }
}
