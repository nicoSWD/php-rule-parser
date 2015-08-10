<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Tokens\TokenArray;

/**
 * Class NodeArray
 * @package nicoSWD\Rules\AST
 */
final class NodeArray extends BaseNode
{
    /**
     * @return \nicoSWD\Rules\Tokens\BaseToken
     * @throws \nicoSWD\Rules\Exceptions\ParserException
     */
    public function getNode()
    {
        $stack = $this->ast->getStack();
        $offset = $stack->current()->getOffset();
        $token = new TokenArray($this->getCommaSeparatedValues(']'), $offset, $stack);

        while ($this->hasMethodCall()) {
            $token = $this->getMethod($token)->call($this->getCommaSeparatedValues());
        }

        return $token;
    }
}
