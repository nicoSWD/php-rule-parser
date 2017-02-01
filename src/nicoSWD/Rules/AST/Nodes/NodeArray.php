<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Tokens\{BaseToken, TokenArray};

final class NodeArray extends BaseNode
{
    /**
     * @throws \nicoSWD\Rules\Exceptions\ParserException
     */
    public function getNode() : BaseToken
    {
        $stack = $this->ast->getStack();
        $offset = $stack->current()->getOffset();
        $token = new TokenArray($this->getArrayItems(), $offset, $stack);

        while ($this->hasMethodCall()) {
            $token = $this->getMethod($token)->call(...$this->getArguments());
        }

        return $token;
    }
}
