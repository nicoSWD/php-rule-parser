<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenString;

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
        $stack = $this->ast->getStack();
        $offset = $stack->current()->getOffset();

        $current = $stack->current();
        $method = rtrim($current->getValue(), " \r\n(");
        $args = [];

        do {
            $stack->next();
            $value = $stack->current()->getValue();

            if ($value === ')') {
                break;
            }

            $args[] = $value;
        } while ($stack->valid());

        $args = $args ? $args[0] : '';

        $token = new TokenString("'{$args}'", $offset, $stack);
        $token->{$method}();

        return $token;
    }
}
