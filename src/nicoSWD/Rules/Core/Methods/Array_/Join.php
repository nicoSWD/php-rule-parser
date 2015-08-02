<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods\Array_;

use nicoSWD\Rules\Core\Methods\CallableMethod;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens;

/**
 * Class Join
 * @package nicoSWD\Rules\Core\Methods\Array_
 */
final class Join implements CallableMethod
{
    /**
     * @param Tokens\BaseToken $token
     * @param mixed[]          $parameters
     * @return Tokens\TokenString
     * @throws ParserException
     */
    public function call(Tokens\BaseToken $token, array $parameters = [])
    {
        if (!isset($parameters[0])) {
            $parameters[0] = ',';
        }

        return new Tokens\TokenString(
            '"' . implode($parameters[0], $token->getValue()) . '"',
            $token->getOffset(),
            $token->getStack()
        );
    }
}
