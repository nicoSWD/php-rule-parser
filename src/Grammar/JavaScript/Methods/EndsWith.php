<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\TokenBool;

final class EndsWith extends CallableFunction
{
    public function call(mixed ...$parameters): TokenBool
    {
        if (!is_string($this->token)) {
            throw new ParserException('Call to undefined method "endsWith" on non-string');
        }

        $needle = $this->parseParameter($parameters, numParam: 0);

        if ($needle === null) {
            return TokenBool::fromBool(false);
        }

        return TokenBool::fromBool(str_ends_with($this->token, $needle));
    }
}
