<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Tokens;

final class TokenArray extends BaseToken
{
    public function getType(): int
    {
        return TokenType::VALUE;
    }

    public function supportsMethodCalls(): bool
    {
        return true;
    }

    public function toArray(): array
    {
        $items = [];

        foreach ($this->value as $value) {
            /* @var self $value */
            $items[] = $value->getValue();
        }

        return $items;
    }
}
