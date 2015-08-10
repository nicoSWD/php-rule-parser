<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class Replace
 * @package nicoSWD\Rules\Core\Methods
 */
final class Replace extends CallableMethod
{
    /**
     * @param mixed[] $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(array $parameters = [])
    {
        if (!array_key_exists(0, $parameters)) {
            $search = '';
        } else {
            $search = $parameters[0];
        }

        if (!array_key_exists(1, $parameters)) {
            $replace = 'undefined';
        } else {
            $replace = $parameters[1];
        }

        return new TokenString(
            '"' . str_replace($search, $replace, $this->token->getValue()) . '"',
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
