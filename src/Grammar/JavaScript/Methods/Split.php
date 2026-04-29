<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class Split extends CallableFunction
{
    public function call(mixed ...$parameters): BaseToken
    {
        $separator = $this->parseParameter($parameters, numParam: 0);

        if (!is_string($separator)) {
            $newValue = [$this->token];
        } else {
            $params = [$separator, $this->token];
            $limit = $this->parseParameter($parameters, numParam: 1);

            if ($limit !== null) {
                $params[] = (int) $limit;
            }

            if ($this->isRegex($separator)) {
                $func = 'preg_split';
            } else {
                $func = 'explode';
            }

            $newValue = $func(...$params);
        }

        return new TokenFactory()->createFromPHPType($newValue);
    }

    private function isRegex(string $value): bool
    {
        return (bool) preg_match('~^/.+/[img]{0,3}$~', $value);
    }
}
