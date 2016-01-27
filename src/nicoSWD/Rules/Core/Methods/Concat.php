<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\{TokenArray, TokenString};
use nicoSWD\Rules\Tokens;

final class Concat extends CallableFunction
{
    /**
     * @param Tokens\BaseToken $parameters
     * @param Tokens\BaseToken $parameters...
     * @return TokenString
     */
    public function call($parameters = null) : TokenString
    {
        $value = $this->token->getValue();
        /** @var Tokens\BaseToken[] $parameters */
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            if ($parameter instanceof TokenArray) {
                $value .= implode(',', $parameter->toArray());
            } else {
                $value .= $parameter->getValue();
            }
        }

        return new TokenString(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'concat';
    }
}
