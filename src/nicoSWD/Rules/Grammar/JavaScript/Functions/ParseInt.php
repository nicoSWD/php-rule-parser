<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Grammar\JavaScript\Functions;

use nicoSWD\Rules\Grammar\CallableFunction;
use nicoSWD\Rules\Grammar\CallableUserFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenInteger;

final class ParseInt extends CallableFunction implements CallableUserFunction
{
    /**
     * @param BaseToken $value
     * @param BaseToken $value ...
     *
     * @return BaseToken
     */
    public function call($value = null): BaseToken
    {
        if ($value === null) {
            return new TokenInteger(NAN);
        }

        return new TokenInteger((int) $value->getValue());
    }
}
