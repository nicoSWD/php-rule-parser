<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Compiler;

use nicoSWD\Rule\Compiler\Exception\MissingOperatorException;
use nicoSWD\Rule\Evaluator\Boolean;
use nicoSWD\Rule\Evaluator\Operator;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenAnd;
use nicoSWD\Rule\TokenStream\Token\TokenOpeningParenthesis;

class StandardCompiler implements CompilerInterface
{
    private const OPENING_PARENTHESIS = '(';
    private const CLOSING_PARENTHESIS = ')';

    private string $output = '';
    private int $openParenthesis = 0;
    private int $closedParenthesis = 0;

    /** @throws ParserException */
    public function getCompiledRule(): string
    {
        if ($this->isIncompleteCondition()) {
            throw new ParserException('Incomplete condition');
        } elseif (!$this->numParenthesesMatch()) {
            throw new ParserException('Missing closing parenthesis');
        }

        return $this->output;
    }

    private function openParenthesis(): void
    {
        $this->openParenthesis++;
        $this->output .= self::OPENING_PARENTHESIS;
    }

    /** @throws ParserException */
    private function closeParenthesis(): void
    {
        if ($this->openParenthesis < 1) {
            throw new ParserException('Missing opening parenthesis');
        }

        $this->closedParenthesis++;
        $this->output .= self::CLOSING_PARENTHESIS;
    }

    /** @throws ParserException */
    public function addParentheses(BaseToken $token): void
    {
        if ($token instanceof TokenOpeningParenthesis) {
            if (!$this->expectOpeningParenthesis()) {
                throw ParserException::unexpectedToken($token);
            }
            $this->openParenthesis();
        } else {
            $this->closeParenthesis();
        }
    }

    /** @throws ParserException */
    public function addLogical(BaseToken $token): void
    {
        $lastChar = Operator::tryFrom($this->getLastChar());

        if ($lastChar !== null) {
            throw ParserException::unexpectedToken($token);
        }

        if ($token instanceof TokenAnd) {
            $this->output .= Operator::LOGICAL_AND->value;
        } else {
            $this->output .= Operator::LOGICAL_OR->value;
        }
    }

    /** @throws MissingOperatorException */
    public function addBoolean(bool $bool): void
    {
        $lastChar = Boolean::tryFrom($this->getLastChar());

        if ($lastChar !== null) {
            throw new MissingOperatorException();
        }

        $this->output .= $bool ? Boolean::BOOL_TRUE->value : Boolean::BOOL_FALSE->value;
    }

    private function numParenthesesMatch(): bool
    {
        return $this->openParenthesis === $this->closedParenthesis;
    }

    private function isIncompleteCondition(): bool
    {
        return Operator::tryFrom($this->getLastChar()) !== null;
    }

    private function expectOpeningParenthesis(): bool
    {
        $lastChar = $this->getLastChar();

        return
            $lastChar === '' ||
            $lastChar === self::OPENING_PARENTHESIS ||
            Operator::tryFrom($lastChar) !== null;
    }

    private function getLastChar(): string
    {
        return substr($this->output, offset: -1);
    }
}
