<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

/**
 * Class NodeString
 * @package nicoSWD\Rules\AST
 */
final class NodeString extends BaseNode
{
    /**
     * @return \nicoSWD\Rules\Tokens\BaseToken
     */
    public function getNode()
    {
        $current = $this->ast->getStack()->current();

        while ($current->supportsMethodCalls() && $this->hasMethodCall()) {
            $current = $this->getMethod($current)->call($this->getCommaSeparatedValues());
        }

        return $current;
    }
}
