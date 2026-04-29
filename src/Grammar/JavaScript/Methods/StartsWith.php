<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\TokenBool;

final class StartsWith extends CallableFunction
{
    public function call(mixed ...$parameters): TokenBool
    {
        if (!is_string($this->token)) {
            throw new ParserException('Call to undefined method "startsWith" on non-string');
        }

        $needle = $this->parseParameter($parameters, numParam: 0);
        $offset = $this->getOffset($this->parseParameter($parameters, numParam: 1));
        $position = strpos($this->token, $needle, $offset);

        return TokenBool::fromBool($position === $offset);
    }

    private function getOffset(mixed $offset): int
    {
        if ($offset !== null) {
            return (int) $offset;
        }

        return 0;
    }
}
