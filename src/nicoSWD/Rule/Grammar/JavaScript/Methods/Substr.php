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
use nicoSWD\Rule\TokenStream\Token\TokenString;

final class Substr extends CallableFunction
{
    /**
     * @param BaseToken $start
     * @param BaseToken $offset
     * @return BaseToken
     */
    public function call($start = null, $offset = null): BaseToken
    {
        $params = [];

        if (!$start) {
            $params[] = 0;
        } else {
            $params[] = (int) $start->getValue();
        }

        if ($offset) {
            $params[] = (int) $offset->getValue();
        }

        $value = substr($this->token->getValue(), ...$params);

        return new TokenString(
            (string) $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
