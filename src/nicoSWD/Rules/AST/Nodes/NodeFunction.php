<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\BaseToken;

/**
 * Class NodeFunction
 * @package nicoSWD\Rules\AST
 */
final class NodeFunction extends BaseNode
{
    /**
     * @return BaseToken
     * @throws ParserException
     */
    public function getNode()
    {
        $current = $this->ast->getStack()->current();
        $function = rtrim($current->getValue(), " \r\n(");
        $class = '\nicoSWD\Rules\Core\Functions\\' . ucfirst($function);

        if (!class_exists($class)) {
            throw new ParserException(sprintf(
                '%s is not defined',
                $function
            ));
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
