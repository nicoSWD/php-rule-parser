<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokenizer;

use nicoSWD\Rules\TokenStream\Token\BaseToken;
use SplObjectStorage;

class TokenStack extends SplObjectStorage
{
    /** @return BaseToken|null */
    public function current()
    {
        return parent::current();
    }

    public function getClone(): self
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
