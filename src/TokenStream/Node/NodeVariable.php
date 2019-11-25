<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Node;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

final class NodeVariable extends BaseNode
{
    public function getNode(): BaseToken
    {
        $token = $this->tokenStream->getVariable($this->getVariableName());

        while ($this->hasMethodCall()) {
            $token = $this->getMethod($token)->call(...$this->getArguments());
        }

        return $token;
    }

    private function getVariableName(): string
    {
        return $this->getCurrentNode()->getOriginalValue();
    }
}
