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
 * Class Substr
 * @package nicoSWD\Rules\Core\Methods
 */
final class Substr extends CallableMethod
{
    /**
     * @param mixed[] $parameters
     * @return TokenString
     */
    public function call(array $parameters = [])
    {
        $params = [];

        if (!array_key_exists(0, $parameters)) {
            $params[] = 0;
        } else {
            $params[] = (int) $parameters[0];
        }

        if (array_key_exists(1, $parameters)) {
            $params[] = (int) $parameters[1];
        }

        $value = call_user_func_array('substr', array_merge([$this->token->getValue()], $params));

        return new TokenString('"' . $value . '"', $this->token->getOffset(), $this->token->getStack());
    }
}
