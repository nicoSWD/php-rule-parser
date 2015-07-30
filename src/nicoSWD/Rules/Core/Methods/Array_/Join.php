<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods\Array_;

use nicoSWD\Rules\Core\Methods\CallableMethod;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class Join
 * @package nicoSWD\Rules\Core\Methods\Array_
 */
class Join implements CallableMethod
{
    /**
     * @param BaseToken $token
     * @param array     $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(BaseToken $token, array $parameters = [])
    {
        if (($numArgs = count($parameters)) !== 1) {
            throw new ParserException(sprintf(
                'Method %s expected 1 argument, got %d',
                __METHOD__,
                $numArgs
            ));
        }

        return new TokenString(
            '"' . implode($parameters[0], $token->getValue()) . '"',
            $token->getOffset(),
            $token->getStack()
        );
    }
}
