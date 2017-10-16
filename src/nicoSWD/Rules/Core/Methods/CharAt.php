<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenInteger;
use nicoSWD\Rules\Tokens\TokenString;

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
        }
        elseif (!$offset instanceof TokenInteger) {
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

    public function getName() : string
    {
        return 'charAt';
    }
}
