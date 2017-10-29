<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Grammar\JavaScript\Methods;

use nicoSWD\Rules\Grammar\CallableFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenInteger;

final class IndexOf extends CallableFunction
{
    /**
     * @param BaseToken $needle
     * @return BaseToken
     */
    public function call($needle = null): BaseToken
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
}
