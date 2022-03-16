<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Token\Type\Value;

final class TokenInteger extends BaseToken implements Value
{
    public function getType(): TokenType
    {
        return TokenType::VALUE;
    }

    public function getValue(): int
    {
        return (int) parent::getValue();
    }
}
