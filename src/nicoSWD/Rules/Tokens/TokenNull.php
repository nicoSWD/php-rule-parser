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

final class TokenNull extends BaseToken
{
    public function getType(): int
    {
        return TokenType::VALUE;
    }

    public function getValue()
    {
    }
}
