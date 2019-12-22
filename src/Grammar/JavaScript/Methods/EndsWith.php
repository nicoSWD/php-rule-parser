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

final class EndsWith extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $needle = $this->parseParameter($parameters, 0);
        $value = strpos($this->token->getValue(), $needle->getValue());

        return new TokenBool($value === strlen($needle->getValue()) - 1);
    }
}
