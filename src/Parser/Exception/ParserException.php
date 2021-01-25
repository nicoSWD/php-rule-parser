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
    public static function unexpectedToken(BaseToken $token): self
    {
        return new self(sprintf('Unexpected "%s" at position %d', $token->getValue(), $token->getOffset()));
    }

    public static function unknownToken(BaseToken $token): self
    {
        return new self(sprintf('Unknown token "%s" at position %d', $token->getValue(), $token->getOffset()));
    }

    public static function incompleteExpression(BaseToken $token): self
    {
        return new self(sprintf('Incomplete expression for token "%s"', $token->getValue()));
    }

    public static function undefinedVariable(string $name, BaseToken $token): self
    {
        return new self(sprintf('Undefined variable "%s" at position %d', $name, $token->getOffset()));
    }

    public static function undefinedMethod(string $name, BaseToken $token): self
    {
        return new self(sprintf('Undefined method "%s" at position %d', $name, $token->getOffset()));
    }

    public static function forbiddenMethod(string $name, BaseToken $token): self
    {
        return new self(sprintf('Forbidden method "%s" at position %d', $name, $token->getOffset()));
    }

    public static function undefinedFunction(string $name, BaseToken $token): self
    {
        return new self(sprintf('%s is not defined at position %d', $name, $token->getOffset()));
    }

    public static function unexpectedComma(BaseToken $token): self
    {
        return new self(sprintf('Unexpected "," at position %d', $token->getOffset()));
    }

    public static function unexpectedEndOfString(): self
    {
        return new self('Unexpected end of string');
    }

    public static function unsupportedType(string $type): self
    {
        return new self(sprintf('Unsupported PHP type: "%s"', $type));
    }

    public static function unknownOperator(BaseToken $token): self
    {
        return new self(
            sprintf('Unexpected operator %s at position %d', $token->getOriginalValue(), $token->getOffset())
        );
    }

    public static function unknownTokenName(string $tokenName): self
    {
        return new self("Unknown token $tokenName");
    }
}
