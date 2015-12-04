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
use nicoSWD\Rules\Tokens\{TokenArray, TokenRegex};
use nicoSWD\Rules\Tokens;

final class Split extends CallableFunction
{
    public function call(TokenCollection $parameters) : TokenArray
    {
        $separator = $parameters->current();

        if (!$separator || !is_string($separator->getValue())) {
            $newValue = [$this->token->getValue()];
        } else {
            $params = [$separator->getValue(), $this->token->getValue()];

            if ($parameters->count() >= 2) {
                $parameters->next();
                $params[] = (int) $parameters->current()->getValue();
            }

            if ($separator instanceof TokenRegex) {
                $func = 'preg_split';
            } else {
                $func = 'explode';
            }

            $newValue = call_user_func_array($func, $params);
        }

        return new TokenArray(
            $newValue,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'split';
    }
}
