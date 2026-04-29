<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class IndexOf extends CallableFunction
{
    public function call(mixed ...$parameters): GenericToken
    {
        $needle = $this->parseParameter($parameters, numParam: 0);

        if ($needle === null) {
            $value = -1;
        } else {
            $value = strpos($this->token, $needle);

            if ($value === false) {
                $value = -1;
            }
        }

        return new GenericToken(TokenKind::INTEGER, $value);
    }
}
