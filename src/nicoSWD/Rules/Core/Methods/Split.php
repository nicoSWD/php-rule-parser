<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Tokens\TokenArray;

/**
 * Class Split
 * @package nicoSWD\Rules\Core\Methods
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
        $tokenValue = $this->token->getValue();

        if (!array_key_exists(0, $parameters) || !is_string($parameters[0])) {
            $newValue = [$tokenValue];
        } else {
            $newValue = explode($parameters[0], $tokenValue);
        }

        return new TokenArray(
            $newValue,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
