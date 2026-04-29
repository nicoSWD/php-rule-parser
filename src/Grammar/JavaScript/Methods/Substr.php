<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class Substr extends CallableFunction
{
    public function call(mixed ...$parameters): GenericToken
    {
        $start = $this->parseParameter($parameters, numParam: 0);
        $params = [(int) $start];

        $offset = $this->parseParameter($parameters, numParam: 1);

        if ($offset !== null) {
            $params[] = (int) $offset;
        }

        $value = substr($this->token, ...$params);

        return new GenericToken(TokenKind::STRING, $value);
    }
}
