<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokens;

use nicoSWD\Rules\Token;
use nicoSWD\Rules\TokenType;

final class TokenNotEqualStrict extends BaseToken
{
    public function getType() : int
    {
        return TokenType::OPERATOR;
    }

    public function getValue()
    {
        return Token::NOT_EQUAL_STRICT;
    }
}
