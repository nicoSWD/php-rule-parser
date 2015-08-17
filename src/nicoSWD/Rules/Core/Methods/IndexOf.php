<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Tokens\TokenInteger;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\Core\CallableFunction;

/**
 * Class IndexOf
 * @package nicoSWD\Rules\Core\Methods
 */
final class IndexOf extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(TokenCollection $parameters)
    {
        $parameters->rewind();

        if ($parameters->count() < 1) {
            $value = -1;
        } else {
            $value = strpos($this->token->getValue(), $parameters->current()->getValue());

            if ($value === \false) {
                $value = -1;
            }
        }

        return new TokenInteger(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'indexOf';
    }
}
