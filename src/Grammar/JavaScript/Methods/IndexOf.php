<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenInteger;

final class IndexOf extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $needle = $this->parseParameter($parameters, numParam: 0);

        if (!$needle) {
            $value = -1;
        } else {
            $value = strpos($this->token->getValue(), $needle->getValue());

            if ($value === false) {
                $value = -1;
            }
        }

        return new TokenInteger($value);
    }
}
