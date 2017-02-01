<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Tokens\BaseToken;
use SplObjectStorage;

final class TokenCollection extends SplObjectStorage
{
    /**
     * @return BaseToken|null
     */
    public function current()
    {
        return parent::current();
    }

    public function toArray() : array
    {
        $items = [];

        foreach ($this as $item) {
            $items[] = $item->getValue();
        }

        return $items;
    }
}
