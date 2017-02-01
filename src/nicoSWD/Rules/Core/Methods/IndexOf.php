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

final class IndexOf extends CallableFunction
{
    /**
     * @param BaseToken $needle
     * @return TokenInteger
     */
    public function call($needle = null) : TokenInteger
    {
        if (!$needle) {
            $value = -1;
        } else {
            $value = strpos($this->token->getValue(), $needle->getValue());

            if ($value === false) {
                $value = -1;
            }
        }

        return new TokenInteger(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'indexOf';
    }
}
