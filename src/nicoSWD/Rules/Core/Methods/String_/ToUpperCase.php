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
 * Class ToUpperCase
 * @package nicoSWD\Rules\Core\Methods\String_
 */
final class ToUpperCase implements CallableMethod
{
    /**
     * @param BaseToken $token
     * @param array     $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(BaseToken $token, array $parameters = [])
    {
        if ($parameters) {
            throw new \Exception;
        }

        return new TokenString('"' . strtoupper($token->getValue()) . '"', $token->getOffset(), $token->getStack());
    }
}
