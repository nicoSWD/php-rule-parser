<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\BaseToken;

final class NodeFunction extends BaseNode
{
    /**
     * @throws ParserException
     */
    public function getNode() : BaseToken
    {
        $current = $this->ast->getStack()->current();
        $function = $this->resolveFunctionName($current);
        $class = $this->resolveClassName($function);

        if (!class_exists($class)) {
            if (!$userFunction = $this->ast->parser->getFunction($function)) {
                throw new ParserException(sprintf(
                    '%s is not defined',
                    $function
                ));
            }

            return $userFunction->call($this, ...$this->getArguments());
        }

        /** @var CallableFunction $instance */
        $instance = new $class($current);

        if ($instance->getName() !== $function) {
            throw new ParserException(sprintf(
                '%s is not defined',
                $function
            ));
        }

        return $instance->call(...$this->getArguments());
    }

    private function resolveClassName(string $class) : string
    {
        return '\nicoSWD\Rules\Core\Functions\\' . ucfirst($class);
    }

    private function resolveFunctionName(BaseToken $token) : string
    {
        return rtrim($token->getValue(), " \r\n(");
    }
}
