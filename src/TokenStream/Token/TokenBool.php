<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

abstract class TokenBool extends BaseToken
{
    public static function fromBool(bool $bool): TokenBool
    {
        return match ($bool) {
            true => new TokenBoolTrue(true),
            false => new TokenBoolFalse(false),
        };
    }

    public function getType(): int
    {
        return TokenType::VALUE;
    }
}
