<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods\String_;

use nicoSWD\Rules\Core\Methods\CallableMethod;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class CharAt
 * @package nicoSWD\Rules\Core\Methods\String_
 */
class CharAt implements CallableMethod
{
    /**
     * @param BaseToken $token
     * @param array     $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(BaseToken $token, array $parameters = [])
    {
        if (!$parameters) {
            throw new \Exception;
        }

        return new TokenString('"' . $token->getValue()[$parameters[0]] . '"', $token->getOffset(), $token->getStack());
    }
}
