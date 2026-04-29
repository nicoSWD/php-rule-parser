<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream\Token;

class TokenString extends BaseToken
{
    public function getKind(): TokenKind
    {
        return TokenKind::STRING;
    }
}
