<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\TokenStream\Nodes;

use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenArray;

final class NodeArray extends BaseNode
{
    public function getNode(): BaseToken
    {
        $stack = $this->tokenStream->getStack();
        $offset = $stack->current()->getOffset();
        $token = new TokenArray($this->getArrayItems(), $offset, $stack);

        while ($this->hasMethodCall()) {
            $token = $this->getMethod($token)->call(...$this->getArguments());
        }

        return $token;
    }
}
