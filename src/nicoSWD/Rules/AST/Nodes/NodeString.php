<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Tokens\BaseToken;

final class NodeString extends BaseNode
{
    public function getNode(): BaseToken
    {
        $current = $this->getCurrentNode();

        while ($current->supportsMethodCalls() && $this->hasMethodCall()) {
            $current = $this->getMethod($current)->call(...$this->getArguments());
        }

        return $current;
    }
}
