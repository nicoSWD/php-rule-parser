<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Exceptions;

use nicoSWD\Rules\Tokens\BaseToken;

class ParserException extends \Exception
{
    public static function unexpectedToken(BaseToken $token): self
    {
        return new self(sprintf(
            'Unexpected "%s" at position %d on line %d',
            $token->getValue(),
            $token->getPosition(),
            $token->getLine()
        ));
    }

    public static function unknownToken(BaseToken $token): self
    {
        return new self(sprintf(
            'Unknown token "%s" at position %d on line %d',
            $token->getOriginalValue(),
            $token->getPosition(),
            $token->getLine()
        ));
    }

    public static function incompleteExpression(BaseToken $token): self
    {
        return new self(sprintf(
            'Incomplete expression for token "%s" at position %d on line %d',
            $token->getOriginalValue(),
            $token->getPosition(),
            $token->getLine()
        ));
    }
}
