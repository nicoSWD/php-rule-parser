<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenString;

final class CharAt extends CallableFunction
{
    public function call(TokenCollection $parameters) : TokenString
    {
        if ($parameters->count() < 1) {
            $offset = 0;
        } else {
            $offset = (int) $parameters->current()->getValue();
        }

        $tokenValue = $this->token->getValue();

        if (!isset($tokenValue[$offset])) {
            $char = '';
        } else {
            $char = $tokenValue[$offset];
        }

        return new TokenString(
            $char,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'charAt';
    }
}
