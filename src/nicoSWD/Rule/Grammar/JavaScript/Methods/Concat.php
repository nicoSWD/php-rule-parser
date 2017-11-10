<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenArray;
use nicoSWD\Rule\TokenStream\Token\TokenString;

final class Concat extends CallableFunction
{
    /**
     * @param BaseToken $parameters
     * @param BaseToken $parameters...
     * @return BaseToken
     */
    public function call($parameters = null): BaseToken
    {
        $value = $this->token->getValue();

        /** @var BaseToken[] $parameters */
        $parameters = func_get_args();

        foreach ($parameters as $parameter) {
            if ($parameter instanceof TokenArray) {
                $value .= implode(',', $parameter->toArray());
            } else {
                $value .= $parameter->getValue();
            }
        }

        return new TokenString($value);
    }
}
