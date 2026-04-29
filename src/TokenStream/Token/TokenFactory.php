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
            'string' => new TokenString($value),
            'integer' => new TokenInteger($value),
            'boolean' => TokenBool::fromBool($value),
            'NULL' => new TokenNull($value),
            'double' => new TokenFloat($value),
            'object' => new TokenObject($value),
            'array' => $this->buildTokenCollection($value),
            default => throw ParserException::unsupportedType(gettype($value)),
        };
    }

    public function createFromToken(TokenKind $kind, array $matches, int $offset): BaseToken
    {
        $args = [$matches[$kind->value], $offset];

        return match ($kind) {
            TokenKind::BOOL_TRUE => new TokenBoolTrue(...$args),
            TokenKind::BOOL_FALSE => new TokenBoolFalse(...$args),
            TokenKind::NULL => new TokenNull(...$args),
            TokenKind::FLOAT => new TokenFloat(...$args),
            TokenKind::INTEGER => new TokenInteger(...$args),
            TokenKind::ENCAPSED_STRING => new TokenEncapsedString(...$args),
            TokenKind::REGEX => new TokenRegex(...$args),
            TokenKind::VARIABLE => new TokenVariable(...$args),
            TokenKind::METHOD => new TokenMethod(...$args),
            TokenKind::FUNCTION => new TokenFunction(...$args),
            default => new GenericToken($kind, ...$args),
        };
    }

    /** @throws ParserException */
    private function buildTokenCollection(array $items): TokenArray
    {
        $tokenCollection = new TokenCollection();

        foreach ($items as $item) {
            $tokenCollection->add($this->createFromPHPType($item));
        }

        return new TokenArray($tokenCollection);
    }
}
