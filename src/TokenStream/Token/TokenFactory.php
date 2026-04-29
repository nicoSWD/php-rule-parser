<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream\Token;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\TokenCollection;

class TokenFactory
{
    /** @throws ParserException */
    public function createFromPHPType(mixed $value): BaseToken
    {
        return match (gettype($value)) {
            'string' => new GenericToken(TokenKind::STRING, $value),
            'integer' => new GenericToken(TokenKind::INTEGER, $value),
            'boolean' => new GenericToken($value ? TokenKind::BOOL_TRUE : TokenKind::BOOL_FALSE, $value),
            'NULL' => new GenericToken(TokenKind::NULL, null),
            'double' => new GenericToken(TokenKind::FLOAT, $value),
            'object' => new GenericToken(TokenKind::OBJECT, $value),
            'array' => $this->buildTokenCollection($value),
            default => throw ParserException::unsupportedType(gettype($value)),
        };
    }

    public function createFromToken(TokenKind $kind, array $matches, int $offset): BaseToken
    {
        $args = [$matches[$kind->value], $offset];

        return match ($kind) {
            TokenKind::ENCAPSED_STRING => new TokenEncapsedString(...$args),
            default => new GenericToken($kind, ...$args),
        };
    }

    /** @throws ParserException */
    private function buildTokenCollection(array $items): GenericToken
    {
        $tokenCollection = new TokenCollection();

        foreach ($items as $item) {
            $tokenCollection->add($this->createFromPHPType($item));
        }

        return new GenericToken(TokenKind::ARRAY, $tokenCollection);
    }
}
