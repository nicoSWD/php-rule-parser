<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

final class TokenFloat extends BaseToken
{
    public function getType(): int
    {
        return TokenType::VALUE;
    }

    public function getValue(): float
    {
        return (float) parent::getValue();
    }
}
