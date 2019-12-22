<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use SplObjectStorage;

final class TokenCollection extends SplObjectStorage
{
    public function current(): BaseToken
    {
        /** @var BaseToken $token */
        $token = parent::current();

        return $token;
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
