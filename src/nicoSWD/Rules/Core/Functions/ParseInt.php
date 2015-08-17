<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Functions;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenInteger;

/**
 * Class ParseInt
 * @package nicoSWD\Rules\Core\Methods
 */
final class ParseInt extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenInteger
     */
    public function call(TokenCollection $parameters)
    {
        $parameters->rewind();

        return new TokenInteger(
            (int) $parameters->current()->getValue(),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'parseInt';
    }
}
