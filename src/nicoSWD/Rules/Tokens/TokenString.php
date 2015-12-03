<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;

class TokenString extends BaseToken
{
    public function getGroup() : int
    {
        return Constants::GROUP_VALUE;
    }

    public function supportsMethodCalls() : bool
    {
        return true;
    }
}
