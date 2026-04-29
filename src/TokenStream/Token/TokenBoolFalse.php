<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

final class TokenBoolFalse extends TokenBool
{
    public function getKind(): TokenKind
    {
        return TokenKind::BOOL_FALSE;
    }

    public function getValue(): bool
    {
        return false;
    }
}
