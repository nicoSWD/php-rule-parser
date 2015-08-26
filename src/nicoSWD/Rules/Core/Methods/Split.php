<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens;

/**
 * Class Split
 * @package nicoSWD\Rules\Core\Methods
 */
final class Split extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return Tokens\TokenArray
     */
    public function call(TokenCollection $parameters)
    {
        $parameters->rewind();
        $separator = $parameters->current();

        if (!$separator || !is_string($separator->getValue())) {
            $newValue = [$this->token->getValue()];
        } else {
            $params = [$separator->getValue(), $this->token->getValue()];

            if ($parameters->count() >= 2) {
                $parameters->next();
                $params[] = (int) $parameters->current()->getValue();
            }

            if ($separator instanceof Tokens\TokenRegex) {
                $func = 'preg_split';
            } else {
                $func = 'explode';
            }

            $newValue = call_user_func_array($func, $params);
        }

        return new Tokens\TokenArray(
            $newValue,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'split';
    }
}
