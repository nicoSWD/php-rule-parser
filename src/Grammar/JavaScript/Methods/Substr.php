<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenString;

final class Substr extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $start = $this->parseParameter($parameters, numParam: 0);
        $params = [(int) $start?->getValue()];

        $offset = $this->parseParameter($parameters, numParam: 1);

        if ($offset) {
            $params[] = (int) $offset->getValue();
        }

        $value = substr($this->token->getValue(), ...$params);

        return new TokenString($value);
    }
}
