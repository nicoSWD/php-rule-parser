<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Tokens\BaseToken;
use SplObjectStorage;

/**
 * Class TokenCollection
 * @package nicoSWD\Rules\AST
 */
final class TokenCollection extends SplObjectStorage
{
    /**
     * @return BaseToken
     */
    public function current()
    {
        return parent::current();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $items = [];

        foreach ($this as $item) {
            $items[] = $item->getValue();
        }

        return $items;
    }
}
