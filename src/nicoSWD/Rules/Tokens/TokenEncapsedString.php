<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Tokens;

final class TokenEncapsedString extends TokenString
{
    public function getValue()
    {
        return substr(parent::getValue(), 1, -1);
    }
}
