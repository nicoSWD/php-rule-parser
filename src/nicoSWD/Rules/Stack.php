<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use SplObjectStorage;

final class Stack extends SplObjectStorage
{
    /**
     * @return \nicoSWD\Rules\Tokens\BaseToken|null
     */
    public function current()
    {
        return parent::current();
    }

    public function getClone() : Stack
    {
        $stackClone = clone $this;
        $stackClone->rewind();

        // This is ugly and needs to be fixed
        while ($stackClone->key() < $this->key()) {
            $stackClone->next();
        }

        return $stackClone;
    }
}
