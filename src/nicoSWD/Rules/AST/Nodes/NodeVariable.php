<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Tokens;

final class NodeVariable extends BaseNode
{
    public function getNode() : Tokens\BaseToken
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
