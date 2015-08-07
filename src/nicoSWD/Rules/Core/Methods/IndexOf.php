<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Tokens\TokenInteger;
use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class IndexOf
 * @package nicoSWD\Rules\Core\Methods
 */
final class IndexOf extends CallableMethod
{
    /**
     * @param mixed[] $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(array $parameters = [])
    {
        if (!array_key_exists(0, $parameters)) {
            $value = -1;
        } else {
            $value = strpos($this->token->getValue(), $parameters[0]);

            if ($value === \false) {
                $value = -1;
            }
        }

        return new TokenInteger($value, $this->token->getOffset(), $this->token->getStack());
    }
}
