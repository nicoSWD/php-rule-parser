<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use nicoSWD\Rules\Tokens\BaseToken;

class Compiler
{
    private $output = '';
    private $openParenthesis = 0;
    private $closedParenthesis = 0;
    private $incompleteCondition = false;
    private $operatorRequired = false;

    public function clear()
    {
        $this->operatorRequired = false;
        $this->incompleteCondition = false;
        $this->output = '';
    }

    public function getCompiledRule()
    {
        if ($this->isIncompleteCondition()) {
            throw new Exceptions\ParserException(
                'Incomplete and/or condition'
            );
        } elseif (!$this->numParenthesesMatch()) {
            throw new Exceptions\ParserException(
                'Missing closing parenthesis'
            );
        }

        return $this->output;
    }

    public function openParentheses(BaseToken $token)
    {
        $this->openParenthesis++;
        $this->output .= '(';
    }

    public function closeParentheses(BaseToken $token)
    {
        if ($this->openParenthesis < 1) {
            throw new Exceptions\ParserException(sprintf(
                'Missing opening parenthesis at position %d on line %d',
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->closedParenthesis++;
        $this->output .= ')';
    }

    public function addParentheses(BaseToken $token)
    {
        if ($token instanceof Tokens\TokenOpeningParenthesis) {
            $lastChar = substr($this->output, -1);

            if ($lastChar !== '' && $lastChar !== '&' && $lastChar !== '|' && $lastChar !== '(') {
                throw Exceptions\ParserException::unexpectedToken($token);
            }
            $this->openParentheses($token);
        } else {
            $this->closeParentheses($token);
        }
    }

    public function addLogical(BaseToken $token)
    {
        if (!$this->operatorRequired) {
            throw Exceptions\ParserException::unexpectedToken($token);
        }

        if ($token instanceof Tokens\TokenAnd) {
            $this->output .= '&';
        } else {
            $this->output .= '|';
        }

        $this->operatorRequired = false;
        $this->incompleteCondition = true;
    }

    public function addBoolean(bool $bool)
    {
        $lastChar = substr($this->output, -1);

        if ($lastChar === '1' || $lastChar === '0') {
            throw new \Exception('Missing operator');
        }

        $this->operatorRequired = true;
        $this->incompleteCondition = false;
        $this->output .= $bool ? '1' : '0';
    }

    private function numParenthesesMatch(): bool
    {
        return $this->openParenthesis === $this->closedParenthesis;
    }

    private function isIncompleteCondition(): bool
    {
        return $this->incompleteCondition;
    }

    public function operatorRequired(bool $bool)
    {
        $this->operatorRequired = $bool;
    }

    public function flipOperatorRequired(BaseToken $token)
    {
        if ($this->operatorRequired) {
            throw new Exceptions\ParserException(sprintf(
                'Missing operator at position %d on line %d',
                $token->getPosition(),
                $token->getLine()
            ));
        }

        $this->operatorRequired = !$this->operatorRequired;
    }
}