<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenString;

final class Substr extends CallableFunction
{
    public function call(TokenCollection $parameters) : TokenString
    {
        $params = [];

        if ($parameters->count() < 1) {
            $params[] = 0;
        } else {
            $params[] = (int) $parameters->current()->getValue();
        }

        if ($parameters->count() >= 2) {
            $parameters->next();
            $params[] = (int) $parameters->current()->getValue();
        }

        $value = call_user_func_array('substr', array_merge([$this->token->getValue()], $params));

        return new TokenString(
            (string) $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'substr';
    }
}
