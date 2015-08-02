<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST;

use nicoSWD\Rules\AST;
use nicoSWD\Rules\Tokens;
use nicoSWD\Rules\Constants;
use nicoSWD\Rules\Exceptions\ParserException;

/**
 * Class BaseNode
 * @package nicoSWD\Rules\AST
 */
abstract class BaseNode
{
    /**
     * @var AST
     */
    protected $ast;

    /**
     * @param AST $ast
     */
    public function __construct(AST $ast)
    {
        $this->ast = $ast;
    }

    /**
     * @return Tokens\BaseToken
     */
    abstract public function getNode();

    /**
     * Looks ahead, but does not move the pointer.
     *
     * @since 0.3.4
     * @return bool
     */
    protected function hasMethodCall()
    {
        $stackClone = $this->ast->getStack()->getClone();
        $hasMethodCall = false;

        while ($stackClone->valid()) {
            $stackClone->next();
            $token = $stackClone->current();

            if ($this->isIgnoredToken($token)) {
                continue;
            } elseif ($token instanceof Tokens\TokenMethod) {
                $hasMethodCall = \true;
                break;
            } else {
                break;
            }
        }

        return $hasMethodCall;
    }

    /**
     * @since 0.3.4
     * @return string
     */
    protected function getMethodName()
    {
        $stack = $this->ast->getStack();
        $methodName = '';

        while ($stack->valid()) {
            $stack->next();

            if (!($token = $stack->current())) {
                break;
            } elseif ($this->isIgnoredToken($token)) {
                continue;
            } elseif ($token instanceof Tokens\TokenMethod) {
                $methodName = ltrim(rtrim($token->getValue(), " \r\n("), '. ');
                break;
            } else {
                break;
            }
        }

        return $methodName;
    }

    /**
     * @since 0.3.4
     * @return mixed[]
     * @throws ParserException
     */
    protected function getFunctionArgs()
    {
        $stack = $this->ast->getStack();
        $current = $stack->current();
        $commaExpected = false;
        $arguments = [];

        while ($stack->valid()) {
            $stack->next();
            $current = $stack->current();
            $value = $current->getValue();

            if ($value === ')') {
                $commaExpected = \false;
                break;
            } elseif ($current->getGroup() === Constants::GROUP_VALUE) {
                if ($commaExpected) {
                    throw new ParserException(sprintf(
                        'Unexpected value at position %d on line %d',
                        $current->getPosition(),
                        $current->getLine()
                    ));
                }

                $commaExpected = \true;
                $arguments[] = $value;
            } elseif ($current instanceof Tokens\TokenComma) {
                if (!$commaExpected) {
                    throw new ParserException(sprintf(
                        'Unexpected token "," at position %d on line %d',
                        $current->getPosition(),
                        $current->getLine()
                    ));
                }

                $commaExpected = \false;
            } elseif (!$this->isIgnoredToken($current)) {
                throw new ParserException('what');
            }
        }

        if ($commaExpected) {
            throw new ParserException(sprintf(
                'Unexpected token "," at position %d on line %d',
                $current->getPosition(),
                $current->getLine()
            ));
        }

        $stack->next();

        return $arguments;
    }

    /**
     * @since 0.3.4
     * @param Tokens\BaseToken $token
     * @return bool
     */
    protected function isIgnoredToken(Tokens\BaseToken $token)
    {
        return (
            $token instanceof Tokens\TokenSpace ||
            $token instanceof Tokens\TokenNewline ||
            $token instanceof Tokens\TokenComment
        );
    }
}
