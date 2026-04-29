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

final class CharAt extends CallableFunction
{
    public function call(mixed ...$parameters): GenericToken
    {
        $offset = $this->parseParameter($parameters, numParam: 0);

        if ($offset === null) {
            $offset = 0;
        } else {
            $offset = (int) $offset;
        }

        $char = ($this->token)[$offset] ?? '';

        return new GenericToken(TokenKind::STRING, $char);
    }
}
