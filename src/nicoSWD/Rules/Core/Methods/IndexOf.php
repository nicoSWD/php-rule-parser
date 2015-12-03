<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Tokens\TokenInteger;
use nicoSWD\Rules\Core\CallableFunction;

final class IndexOf extends CallableFunction
{
    /**
     * @throws \Exception
     */
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
