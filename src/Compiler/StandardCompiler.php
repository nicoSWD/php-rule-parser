<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Compiler;

use nicoSWD\Rule\Compiler\Exception\MissingOperatorException;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenAnd;
use nicoSWD\Rule\TokenStream\Token\TokenOpeningParenthesis;

class StandardCompiler implements CompilerInterface
{
    private const BOOL_TRUE = '1';
    private const BOOL_FALSE = '0';

    private const LOGICAL_AND = '&';
    private const LOGICAL_OR = '|';

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
        $lastChar = $this->getLastChar();

        if ($lastChar === self::LOGICAL_AND || $lastChar === self::LOGICAL_OR) {
            throw ParserException::unexpectedToken($token);
        }

        if ($token instanceof TokenAnd) {
            $this->output .= self::LOGICAL_AND;
        } else {
            $this->output .= self::LOGICAL_OR;
        }
    }

    /** @throws MissingOperatorException */
    public function addBoolean(bool $bool): void
    {
        $lastChar = $this->getLastChar();

        if ($lastChar === self::BOOL_TRUE || $lastChar === self::BOOL_FALSE) {
            throw new MissingOperatorException();
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

        return
            $lastChar === self::LOGICAL_AND ||
            $lastChar === self::LOGICAL_OR;
    }

    private function expectOpeningParenthesis(): bool
    {
        $lastChar = $this->getLastChar();

        return
            $lastChar === '' ||
            $lastChar === self::LOGICAL_AND ||
            $lastChar === self::LOGICAL_OR ||
            $lastChar === self::OPENING_PARENTHESIS;
    }

    private function getLastChar(): string
    {
        return substr($this->output, offset: -1);
    }
}
