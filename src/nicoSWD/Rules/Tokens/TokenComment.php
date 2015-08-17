<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Constants;

/**
 * Class TokenComment
 * @package nicoSWD\Rules\Tokens
 */
final class TokenComment extends BaseToken
{
    /**
     * @return int
     */
    public function getGroup()
    {
        return Constants::GROUP_COMMENT;
    }
}
