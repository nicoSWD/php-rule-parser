<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenString;

final class Substr extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $params = [];
        $start = $this->parseParameter($parameters, numParam: 0);

        if (!$start) {
            $params[] = 0;
        } else {
            $params[] = (int) $start->getValue();
        }

        $offset = $this->parseParameter($parameters, numParam: 1);

        if ($offset) {
            $params[] = (int) $offset->getValue();
        }

        $value = substr($this->token->getValue(), ...$params);

        return new TokenString((string) $value);
    }
}
