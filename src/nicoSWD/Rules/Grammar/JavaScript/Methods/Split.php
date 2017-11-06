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
use nicoSWD\Rules\TokenStream\Token\TokenArray;
use nicoSWD\Rules\TokenStream\Token\TokenRegex;

final class Split extends CallableFunction
{
    /**
     * @param BaseToken $separator
     * @param BaseToken $limit
     * @return BaseToken
     */
    public function call($separator = null, $limit = null): BaseToken
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
}
