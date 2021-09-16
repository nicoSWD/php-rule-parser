<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

final class TokenOpeningParenthesis extends BaseToken
{
    public function getType(): TokenType
    {
        return TokenType::PARENTHESIS;
    }
}
