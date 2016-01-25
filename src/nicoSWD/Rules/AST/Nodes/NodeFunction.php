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
        $function = rtrim($current->getValue(), " \r\n(");
        $class = '\nicoSWD\Rules\Core\Functions\\' . ucfirst($function);

        if (!class_exists($class)) {
            if (!$userFunction = $this->ast->parser->getFunction($function)) {
                throw new ParserException(sprintf(
                    '%s is not defined',
                    $function
                ));
            }

            return $userFunction->call($this, $this->getCommaSeparatedValues());
        }

        /** @var CallableFunction $instance */
        $instance = new $class($current);

        if ($instance->getName() !== $function) {
            throw new ParserException(sprintf(
                '%s is not defined',
                $function
            ));
        }

        return $instance->call($this->getCommaSeparatedValues());
    }
}
