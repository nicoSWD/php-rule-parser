<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;

final class TokenAnd extends BaseToken
{
    public function getGroup() : int
    {
        return Constants::GROUP_LOGICAL;
    }

    public function getValue() : string
    {
        return '&';
    }
}
