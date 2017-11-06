<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Grammar\JavaScript\Methods;

use nicoSWD\Rules\Grammar\CallableFunction;
use nicoSWD\Rules\TokenStream\Token\BaseToken;
use nicoSWD\Rules\TokenStream\Token\TokenInteger;
use nicoSWD\Rules\TokenStream\Token\TokenString;

final class CharAt extends CallableFunction
{
    /**
     * @param BaseToken $offset
     * @return BaseToken
     */
    public function call($offset = null): BaseToken
    {
        $tokenValue = $this->token->getValue();

        if (!$offset) {
            $offset = 0;
        } elseif (!$offset instanceof TokenInteger) {
            $offset = (int) $offset->getValue();
        } else {
            $offset = $offset->getValue();
        }

        if (!isset($tokenValue[$offset])) {
            $char = '';
        } else {
            $char = $tokenValue[$offset];
        }

        return new TokenString(
            $char,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
