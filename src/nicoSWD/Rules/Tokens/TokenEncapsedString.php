<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

/**
 * Class TokenEncapsedString
 * @package nicoSWD\Rules\Tokens
 */
final class TokenEncapsedString extends TokenString
{
    /**
     * @return string
     */
    public function getValue()
    {
        return substr(parent::getValue(), 1, -1);
    }
}
