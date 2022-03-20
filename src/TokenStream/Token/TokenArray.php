<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\TokenStream\Token\Type\Value;

final class TokenArray extends BaseToken implements Value
{
    public function getType(): TokenType
    {
        return TokenType::VALUE;
    }

    public function toArray(): array
    {
        $items = [];

        foreach ($this->getValue() as $value) {
            /** @var self $value */
            $items[] = $value->getValue();
        }

        return $items;
    }
}
