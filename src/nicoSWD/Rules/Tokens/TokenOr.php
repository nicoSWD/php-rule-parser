<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;

final class TokenOr extends BaseToken
{
    public function getGroup() : int
    {
        return Constants::GROUP_LOGICAL;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return '|';
    }
}
