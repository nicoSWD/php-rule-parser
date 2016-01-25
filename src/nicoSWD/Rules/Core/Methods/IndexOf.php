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
use nicoSWD\Rules\Tokens\TokenInteger;

final class IndexOf extends CallableFunction
{
    public function call(TokenCollection $parameters) : TokenInteger
    {
        if ($parameters->count() < 1) {
            $value = -1;
        } else {
            $value = strpos($this->token->getValue(), $parameters->current()->getValue());

            if ($value === false) {
                $value = -1;
            }
        }

        return new TokenInteger(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'indexOf';
    }
}
