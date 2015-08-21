<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\AST\Nodes;

use nicoSWD\Rules\AST;
use nicoSWD\Rules\Core\CallableFunction;
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
     * @return \nicoSWD\Rules\Core\CallableFunction
     * @throws ParserException
     */
    public function getMethod(Tokens\BaseToken $token)
    {
        $methodName = trim($this->getMethodName());
        $methodClass = '\nicoSWD\Rules\Core\Methods\\' . ucfirst($methodName);

        if (!class_exists($methodClass)) {
            $current = $this->ast->getStack()->current();

            throw new ParserException(sprintf(
                'undefined is not a function at position %d on line %d',
                $current->getPosition(),
                $current->getLine()
            ));
        }

        /** @var CallableFunction $instance */
        $instance = new $methodClass($token);

        if ($instance->getName() !== $methodName) {
            throw new ParserException(
                'undefined is not a function'
            );
        }

        return $instance;
    }

    /**
     * @since 0.3.4
     * @internal
     * @return string
     */
    protected function getMethodName()
    {
        do {
            $this->ast->next();
        } while ($this->ast->getStack()->current()->getOffset() < $this->methodOffset);

        return ltrim(rtrim($this->methodName, " \r\n("), '. ');
    }

    /**
     * @since 0.3.4
     * @param string $stopAt
     * @return AST\TokenCollection
     * @throws ParserException
     */
    protected function getCommaSeparatedValues($stopAt = ')')
    {
        $commaExpected = \false;
        $items = new AST\TokenCollection();

        do {
            $this->ast->next();

            if (!$current = $this->ast->current()) {
                throw new ParserException(sprintf(
                    'Unexpected end of string. Expected "%s"',
                    $stopAt
                ));
            }

            if ($current->getGroup() === Constants::GROUP_VALUE) {
                if ($commaExpected) {
                    throw new ParserException(sprintf(
                        'Unexpected value at position %d on line %d',
                        $current->getPosition(),
                        $current->getLine()
                    ));
                }

                $commaExpected = \true;
                $items->attach($current);
            } elseif ($current instanceof Tokens\TokenComma) {
                if (!$commaExpected) {
                    throw new ParserException(sprintf(
                        'Unexpected token "," at position %d on line %d',
                        $current->getPosition(),
                        $current->getLine()
                    ));
                }

                $commaExpected = \false;
            } elseif ($current->getValue() === $stopAt) {
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

        if (!$commaExpected && $items->count() > 0) {
            throw new ParserException(sprintf(
                'Unexpected token "," at position %d on line %d',
                $current->getPosition(),
                $current->getLine()
            ));
        }

        return $items;
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
