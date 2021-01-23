<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenArray;
use nicoSWD\Rule\TokenStream\Token\TokenRegex;

final class Split extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $separator = $this->parseParameter($parameters, numParam: 0);

        if (!$separator || !is_string($separator->getValue())) {
            $newValue = [$this->token->getValue()];
        } else {
            $params = [$separator->getValue(), $this->token->getValue()];
            $limit = $this->parseParameter($parameters, numParam: 1);

            if ($limit !== null) {
                $params[] = (int) $limit->getValue();
            }

            if ($separator instanceof TokenRegex) {
                $func = 'preg_split';
            } else {
                $func = 'explode';
            }

            $newValue = $func(...$params);
        }

        return new TokenArray($newValue);
    }
}
