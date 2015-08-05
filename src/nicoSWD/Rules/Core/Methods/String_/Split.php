<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods\String_;

use nicoSWD\Rules\Core\Methods\CallableMethod;
use nicoSWD\Rules\Tokens\TokenArray;
use nicoSWD\Rules\Exceptions\ParserException;

/**
 * Class Split
 * @package nicoSWD\Rules\Core\Methods\String_
 */
final class Split extends CallableMethod
{
    /**
     * @param mixed[] $parameters
     * @return TokenArray
     * @throws \Exception
     */
    public function call(array $parameters = [])
    {
        if (!array_key_exists(0, $parameters)) {
            $parameters[0] = '';
        }

        return new TokenArray(
            explode($parameters[0], $this->token->getValue()),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
