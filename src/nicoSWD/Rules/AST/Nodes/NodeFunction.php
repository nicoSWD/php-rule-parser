<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Tokens\BaseToken;

final class NodeFunction extends BaseNode
{
    public function getNode(): BaseToken
    {
        $functionName = $this->resolveFunctionName(
            $this->ast->getStack()->current()
        );

        return $this->ast->parser->getFunction($functionName)->call($this, ...$this->getArguments());
    }

    private function resolveFunctionName(BaseToken $token): string
    {
        return rtrim($token->getValue(), " \r\n(");
    }
}
