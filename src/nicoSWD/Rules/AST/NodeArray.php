<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\Constants;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenArray;
use nicoSWD\Rules\Tokens\TokenComma;

/**
 * Class NodeArray
 * @package nicoSWD\Rules\AST
 */
final class NodeArray extends BaseNode
{
    /**
     * @return BaseToken
     * @throws ParserException
     */
    public function getNode()
    {
        $stack = $this->ast->getStack();
        $offset = $stack->current()->getOffset();
        $commaExpected = \false;
        $items = [];

        do {
            $stack->next();

            if (!$current = $stack->current()) {
                throw new ParserException(sprintf(
                    'Unexpected end of string. Expected "]"'
                ));
            }

            $value = $current->getValue();

            if ($current->getGroup() === Constants::GROUP_VALUE) {
                if ($commaExpected) {
                    throw new ParserException(sprintf(
                        'Unexpected value at position %d on line %d',
                        $current->getPosition(),
                        $current->getLine()
                    ));
                }

                $commaExpected = \true;
                $items[] = $value;
            } elseif ($current instanceof TokenComma) {
                if (!$commaExpected) {
                    throw new ParserException(sprintf(
                        'Unexpected token "," at position %d on line %d',
                        $current->getPosition(),
                        $current->getLine()
                    ));
                }

                $commaExpected = \false;
            } elseif ($value === ']') {
                $commaExpected = \false;
                break;
            } elseif (!$this->isIgnoredToken($current)) {
                throw new ParserException(sprintf(
                    'Unexpected token "%s" at position %d on line %d',
                    $current->getOriginalValue(),
                    $current->getPosition(),
                    $current->getLine()
                ));
            }
        } while ($stack->valid());

        if ($commaExpected) {
            throw new ParserException(sprintf(
                'Unexpected token "," at position %d on line %d',
                $current->getPosition(),
                $current->getLine()
            ));
        }

        $token = new TokenArray($items, $offset, $stack);

        while ($this->hasMethodCall()) {
            $method = sprintf(
                '\nicoSWD\Rules\Core\Methods\%s\%s',
                'Array_',
                ucfirst($this->getMethodName())
            );

            /** @var \nicoSWD\Rules\Core\Methods\CallableMethod $instance */
            $instance = new $method();
            $token = $instance->call($token, $this->getFunctionArgs());
        }

       // var_dump($token->getValue());

        return $token;
    }
}
