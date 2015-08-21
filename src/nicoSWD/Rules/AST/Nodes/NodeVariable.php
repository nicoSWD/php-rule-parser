<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Tokens;

/**
 * Class NodeVariable
 * @package nicoSWD\Rules\AST
 */
final class NodeVariable extends BaseNode
{
    /**
     * @return Tokens\BaseToken
     */
    public function getNode()
    {
        $value = $this->ast->getStack()->current()->getValue();
        $current = $this->ast->getVariable($value);

        $current->setOffset($current->getOffset());
        $current->setStack($this->ast->getStack());

        while ($this->hasMethodCall()) {
            $current = $this->getMethod($current)->call($this->getCommaSeparatedValues());
        }

        return $current;
    }
}
