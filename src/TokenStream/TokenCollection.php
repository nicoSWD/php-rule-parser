<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

final class TokenCollection extends \ArrayObject
{
    public function current(): BaseToken
    {
        /** @var BaseToken $token */
        $token = $this->offsetGet($this->key());

        return $token;
    }

    public function add(BaseToken $token): void
    {
        $this->append($token);
    }

    public function toArray(): array
    {
        $items = [];

        foreach ($this as $item) {
            $items[] = $item->getValue();
        }

        return $items;
    }
}
