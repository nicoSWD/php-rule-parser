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
 * Class Concat
 * @package nicoSWD\Rules\Core\Methods
 */
final class Concat extends CallableMethod
{
    /**
     * @param mixed[] $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(array $parameters = [])
    {
        $value = $this->token->getValue();

        foreach ($parameters as $parameter) {
            if (is_array($parameter)) {
                $parameter = implode(',', $parameter);
            }

            $value .= $parameter;
        }

        return new TokenString('"' . $value . '"', $this->token->getOffset(), $this->token->getStack());
    }
}
