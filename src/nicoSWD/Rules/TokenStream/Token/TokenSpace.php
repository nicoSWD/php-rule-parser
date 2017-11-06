<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\TokenStream\Token;

final class TokenSpace extends BaseToken
{
    public function getType(): int
    {
        return TokenType::SPACE;
    }

    public function isWhitespace(): bool
    {
        return true;
    }
}
