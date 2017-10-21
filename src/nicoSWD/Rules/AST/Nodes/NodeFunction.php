<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Grammar\JavaScript\Functions\ParseFloat;
use nicoSWD\Rules\Grammar\JavaScript\Functions\ParseInt;
use nicoSWD\Rules\Tokens\BaseToken;

final class NodeFunction extends BaseNode
{
    public function getNode(): BaseToken
    {
        $parser = $this->getParser();

        $parser->registerFunctionClass(ParseInt::class);
        $parser->registerFunctionClass(ParseFloat::class);

        return $parser->getFunction($this->resolveFunctionName())->call($this, ...$this->getArguments());
    }

    private function resolveFunctionName(): string
    {
        return rtrim($this->getCurrentNode()->getValue(), " \r\n(");
    }
}
