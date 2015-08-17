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
 * Class Substr
 * @package nicoSWD\Rules\Core\Methods
 */
final class Substr extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenString
     */
    public function call(TokenCollection $parameters)
    {
        $parameters->rewind();
        $params = [];

        if ($parameters->count() < 1) {
            $params[] = 0;
        } else {
            $params[] = (int) $parameters->current()->getValue();
        }

        if ($parameters->count() >= 2) {
            $parameters->next();
            $params[] = (int) $parameters->current()->getValue();
        }

        $value = call_user_func_array('substr', array_merge([$this->token->getValue()], $params));

        return new TokenString(
            (string) $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'substr';
    }
}
