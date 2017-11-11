<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

interface CallableUserFunction
{
    public function call(BaseToken $param = null): BaseToken;
}
