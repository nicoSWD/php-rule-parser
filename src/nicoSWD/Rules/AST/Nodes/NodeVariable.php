<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Tokens;
use nicoSWD\Rules\Tokens\BaseToken;

final class NodeVariable extends BaseNode
{
    public function getNode() : BaseToken
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
