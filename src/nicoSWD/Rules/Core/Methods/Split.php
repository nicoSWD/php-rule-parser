<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\{TokenArray, TokenRegex};
use nicoSWD\Rules\Tokens;

final class Split extends CallableFunction
{
    /**
     * @param Tokens\BaseToken $separator
     * @param Tokens\BaseToken $limit
     * @return TokenArray
     */
    public function call($separator = null, $limit = null) : TokenArray
    {
        if (!$separator || !is_string($separator->getValue())) {
            $newValue = [$this->token->getValue()];
        } else {
            $params = [$separator->getValue(), $this->token->getValue()];

            if ($limit) {
                $params[] = (int) $limit->getValue();
            }

            if ($separator instanceof TokenRegex) {
                $func = 'preg_split';
            } else {
                $func = 'explode';
            }

            $newValue = $func(...$params);
        }

        return new TokenArray(
            $newValue,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'split';
    }
}
