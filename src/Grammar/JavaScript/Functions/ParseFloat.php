<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Functions;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFloat;

final class ParseFloat extends CallableFunction implements CallableUserFunctionInterface
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $value = $this->parseParameter($parameters, numParam: 0);

        if (!isset($value)) {
            return new TokenFloat(NAN);
        }

        return new TokenFloat((float) $value->getValue());
    }
}
