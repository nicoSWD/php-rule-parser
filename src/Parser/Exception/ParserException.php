<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Parser\Exception;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

class ParserException extends \Exception
{
    public static function unexpectedToken(BaseToken $token): static
    {
        return new static(sprintf('Unexpected "%s" at position %d', $token->getValue(), $token->getOffset()));
    }

    public static function unknownToken(BaseToken $token): static
    {
        return new static(sprintf('Unknown token "%s" at position %d', $token->getValue(), $token->getOffset()));
    }

    public static function incompleteExpression(BaseToken $token): static
    {
        return new static(sprintf('Incomplete expression for token "%s"', $token->getValue()));
    }

    public static function undefinedVariable(string $name, BaseToken $token): static
    {
        return new static(sprintf('Undefined variable "%s" at position %d', $name, $token->getOffset()));
    }

    public static function undefinedMethod(string $name, BaseToken $token): static
    {
        return new static(sprintf('Undefined method "%s" at position %d', $name, $token->getOffset()));
    }

    public static function forbiddenMethod(string $name, BaseToken $token): static
    {
        return new static(sprintf('Forbidden method "%s" at position %d', $name, $token->getOffset()));
    }

    public static function undefinedFunction(string $name, BaseToken $token): static
    {
        return new static(sprintf('%s is not defined at position %d', $name, $token->getOffset()));
    }

    public static function unexpectedComma(BaseToken $token): static
    {
        return new static(sprintf('Unexpected "," at position %d', $token->getOffset()));
    }

    public static function unexpectedEndOfString(): static
    {
        return new static('Unexpected end of string');
    }

    public static function unsupportedType(string $type): static
    {
        return new static(sprintf('Unsupported PHP type: "%s"', $type));
    }
}
