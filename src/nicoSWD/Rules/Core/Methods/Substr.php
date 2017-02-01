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
use nicoSWD\Rules\Tokens\TokenString;

final class Substr extends CallableFunction
{
    /**
     * @param BaseToken $start
     * @param BaseToken $offset
     * @return TokenString
     */
    public function call($start = null, $offset = null) : TokenString
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

    public function getName() : string
    {
        return 'substr';
    }
}
