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
     * @var array
     */
    protected $methodInstances = [];

    /**
     * @var string
     */
    protected $methodName = '';

    /**
     * @var int
     */
    protected $methodOffset = 0;

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

        while ($stackClone->valid()) {
            $stackClone->next();

            if (!$token = $stackClone->current()) {
                break;
            } elseif ($this->isIgnoredToken($token)) {
                continue;
            } elseif ($token instanceof Tokens\TokenMethod) {
                $this->methodName = $token->getValue();
                $this->methodOffset = $token->getOffset();

                return \true;
            } else {
                break;
            }
        }

        return \false;
    }

    /**
     * @param Tokens\BaseToken $token
     * @return \nicoSWD\Rules\Core\Methods\CallableMethod
     */
    public function getMethod(Tokens\BaseToken $token)
    {
        $method = sprintf(
            '\nicoSWD\Rules\Core\Methods\%s',
            ucfirst(trim($this->getMethodName()))
        );

        if (!isset($this->methodInstances[$method])) {
            $this->methodInstances[$method] = new $method($token);
        }

        return $this->methodInstances[$method];
    }

    /**
     * @since 0.3.4
     * @internal
     * @return string
     */
    protected function getMethodName()
    {
        while ($this->ast->getStack()->current()->getOffset() < $this->methodOffset) {
            $this->ast->next();
        }

        return ltrim(rtrim($this->methodName, " \r\n("), '. ');
    }

    /**
     * @since 0.3.4
     * @return mixed[]
     * @throws ParserException
     */
    protected function getFunctionArgs()
    {
        $commaExpected = \false;
        $arguments = [];

        do {
            $this->ast->next();

            if (!$current = $this->ast->current()) {
                throw new ParserException(sprintf(
                    'Unexpected end of string. Expected ")"'
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
            } elseif ($value === ')') {
                break;
            } elseif (!$this->isIgnoredToken($current)) {
                throw new ParserException(sprintf(
                    'Unexpected token "%s" at position %d on line %d',
                    $current->getOriginalValue(),
                    $current->getPosition(),
                    $current->getLine()
                ));
            }
        } while ($this->ast->valid());

        if (!$commaExpected && $arguments) {
            throw new ParserException(sprintf(
                'Unexpected token "," at position %d on line %d',
                $current->getPosition(),
                $current->getLine()
            ));
        }

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
