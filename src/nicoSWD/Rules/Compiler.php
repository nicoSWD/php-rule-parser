<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenOpeningParenthesis;

class Compiler
{
    const BOOL_TRUE = '1';
    const BOOL_FALSE = '0';

    const LOGICAL_AND = '&';
    const LOGICAL_OR = '|';

    const OPENING_PARENTHESIS = '(';
    const CLOSING_PARENTHESIS = ')';

    private $output = '';
    private $openParenthesis = 0;
    private $closedParenthesis = 0;

    public function clear()
    {
        $this->output = '';
    }

    public function getCompiledRule()
    {
        if ($this->isIncompleteCondition()) {
            throw new Exceptions\ParserException('Incomplete condition');
        } elseif (!$this->numParenthesesMatch()) {
            throw new Exceptions\ParserException('Missing closing parenthesis');
        }

        return $this->output;
    }

    private function openParenthesis()
    {
        $this->openParenthesis++;
        $this->output .= self::OPENING_PARENTHESIS;
    }

    private function closeParenthesis(BaseToken $token)
    {
        if ($this->openParenthesis < 1) {
            throw new Exceptions\ParserException(sprintf(
                'Missing opening parenthesis at position %d on line %d',
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->closedParenthesis++;
        $this->output .= self::CLOSING_PARENTHESIS;
    }

    public function addParentheses(BaseToken $token)
    {
        if ($token instanceof Tokens\TokenOpeningParenthesis) {
            if (!$this->expectOpeningParenthesis()) {
                throw Exceptions\ParserException::unexpectedToken($token);
            }
            $this->openParenthesis();
        } else {
            $this->closeParenthesis($token);
        }
    }

    public function addLogical(BaseToken $token)
    {
        $lastChar = $this->getLastChar();

        if ($lastChar === self::LOGICAL_AND || $lastChar === self::LOGICAL_OR) {
            throw Exceptions\ParserException::unexpectedToken($token);
        }

        if ($token instanceof Tokens\TokenAnd) {
            $this->output .= self::LOGICAL_AND;
        } else {
            $this->output .= self::LOGICAL_OR;
        }
    }

    public function addBoolean(bool $bool)
    {
        $lastChar = $this->getLastChar();

        if ($lastChar === self::BOOL_TRUE || $lastChar === self::BOOL_FALSE) {
            throw new Exceptions\MissingOperatorException();
        }

        $this->output .= $bool ? self::BOOL_TRUE : self::BOOL_FALSE;
    }

    private function numParenthesesMatch(): bool
    {
        return $this->openParenthesis === $this->closedParenthesis;
    }

    private function isIncompleteCondition(): bool
    {
        $lastChar = $this->getLastChar();

        return (
            $lastChar === self::LOGICAL_AND ||
            $lastChar === self::LOGICAL_OR
        );
    }

    private function expectOpeningParenthesis(): bool
    {
        $lastChar = $this->getLastChar();

        return (
            $lastChar === '' ||
            $lastChar === self::LOGICAL_AND ||
            $lastChar === self::LOGICAL_OR ||
            $lastChar === self::OPENING_PARENTHESIS
        );
    }

    private function getLastChar(): string
    {
        return substr($this->output, -1);
    }
}