<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\TokenType;

final class TokenOr extends BaseToken
{
    public function getType(): int
    {
        return TokenType::LOGICAL;
    }
}
