<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenBool;

final class StartsWith extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $needle = $this->parseParameter($parameters, 0);
        $offset = $this->parseParameter($parameters, 1);

        if ($offset) {
            $offset = $offset->getValue();
        } else {
            $offset = 0;
        }

        $value = strpos($this->token->getValue(), $needle->getValue(), $offset);

        return new TokenBool($value === $offset);
    }
}
