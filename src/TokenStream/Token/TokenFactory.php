<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\TokenCollection;

class TokenFactory
{
    /** @var array<string, string> */
    private $tokenMap = [
        Token::AND                 => TokenAnd::class,
        Token::OR                  => TokenOr::class,
        Token::NOT_EQUAL_STRICT    => TokenNotEqualStrict::class,
        Token::NOT_EQUAL           => TokenNotEqual::class,
        Token::EQUAL_STRICT        => TokenEqualStrict::class,
        Token::EQUAL               => TokenEqual::class,
        Token::IN                  => TokenIn::class,
        Token::BOOL                => TokenBool::class,
        Token::NULL                => TokenNull::class,
        Token::METHOD              => TokenMethod::class,
        Token::FUNCTION            => TokenFunction::class,
        Token::VARIABLE            => TokenVariable::class,
        Token::FLOAT               => TokenFloat::class,
        Token::INTEGER             => TokenInteger::class,
        Token::ENCAPSED_STRING     => TokenEncapsedString::class,
        Token::SMALLER_EQUAL       => TokenSmallerEqual::class,
        Token::GREATER_EQUAL       => TokenGreaterEqual::class,
        Token::SMALLER             => TokenSmaller::class,
        Token::GREATER             => TokenGreater::class,
        Token::OPENING_PARENTHESIS => TokenOpeningParenthesis::class,
        Token::CLOSING_PARENTHESIS => TokenClosingParenthesis::class,
        Token::OPENING_ARRAY       => TokenOpeningArray::class,
        Token::CLOSING_ARRAY       => TokenClosingArray::class,
        Token::COMMA               => TokenComma::class,
        Token::REGEX               => TokenRegex::class,
        Token::COMMENT             => TokenComment::class,
        Token::NEWLINE             => TokenNewline::class,
        Token::SPACE               => TokenSpace::class,
        Token::UNKNOWN             => TokenUnknown::class,
    ];

    public function createFromPHPType($value): BaseToken
    {
        switch ($type = gettype($value)) {
            case 'string':
                return new TokenString($value);
            case 'integer':
                return new TokenInteger($value);
            case 'boolean':
                return new TokenBool($value);
            case 'NULL':
                return new TokenNull($value);
            case 'double':
                return new TokenFloat($value);
            case 'array':
                return $this->buildTokenCollection($value);
            case 'object':
                return new TokenObject($value);
            default:
                throw new ParserException(sprintf(
                    'Unsupported PHP type: "%s"',
                    $type
                ));
        }
    }

    public function createFromTokenName(string $tokenName): string
    {
        if (!isset($this->tokenMap[$tokenName])) {
            throw new \InvalidArgumentException("Unknown token $tokenName");
        }

        return $this->tokenMap[$tokenName];
    }

    private function buildTokenCollection($value): TokenArray
    {
        $params = new TokenCollection();

        foreach ($value as $item) {
            $params->attach($this->createFromPHPType($item));
        }

        return new TokenArray($params);
    }
}
