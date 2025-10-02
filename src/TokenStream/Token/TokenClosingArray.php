<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

final class TokenClosingArray extends BaseToken
{
    public function getType(): TokenType
    {
        return TokenType::SQUARE_BRACKET;
    }
}
