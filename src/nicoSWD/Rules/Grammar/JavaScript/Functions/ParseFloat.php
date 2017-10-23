<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Grammar\JavaScript\Functions;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Core\CallableUserFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenFloat;

final class ParseFloat extends CallableFunction implements CallableUserFunction
{
    /**
     * @param BaseToken $value
     * @param BaseToken $value ...
     * @return BaseToken
     */
    public function call($value = null): BaseToken
    {
        if ($value === null) {
            return new TokenFloat(NAN);
        }

        return new TokenFloat((float) $value->getValue());
    }
}
