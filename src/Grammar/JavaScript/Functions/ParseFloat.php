<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar\JavaScript\Functions;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class ParseFloat extends CallableFunction implements CallableInterface
{
    public function call(mixed ...$parameters): GenericToken
    {
        $value = $this->parseParameter($parameters, numParam: 0);

        if ($value === null) {
            return new GenericToken(TokenKind::FLOAT, NAN);
        }

        return new GenericToken(TokenKind::FLOAT, (float) $value);
    }
}
