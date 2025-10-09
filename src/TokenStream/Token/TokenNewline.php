<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Token\Type\Whitespace;

final class TokenNewline extends BaseToken implements Whitespace
{
    public function getType(): TokenType
    {
        return TokenType::SPACE;
    }
}
