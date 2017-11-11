<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Functions;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Grammar\CallableUserFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenInteger;

final class ParseInt extends CallableFunction implements CallableUserFunction
{
    public function call(BaseToken $value = null): BaseToken
    {
        if ($value === null) {
            return new TokenInteger(NAN);
        }

        return new TokenInteger((int) $value->getValue());
    }
}
