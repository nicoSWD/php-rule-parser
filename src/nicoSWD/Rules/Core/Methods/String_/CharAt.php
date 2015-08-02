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
final class CharAt implements CallableMethod
{
    /**
     * @param BaseToken $token
     * @param mixed[]   $parameters
     * @return TokenString
     */
    public function call(BaseToken $token, array $parameters = [])
    {
        if (!array_key_exists(0, $parameters)) {
            $parameters[0] = 0;
        } else {
            $parameters[0] = (int) $parameters[0];
        }

        $value = $token->getValue();

        if (!isset($value[$parameters[0]])) {
            $char = '';
        } else {
            $char = $value[$parameters[0]];
        }

        return new TokenString('"' . $char . '"', $token->getOffset(), $token->getStack());
    }
}
