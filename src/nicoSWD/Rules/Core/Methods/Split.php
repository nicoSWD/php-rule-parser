<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Tokens\TokenArray;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenRegex;

/**
 * Class Split
 * @package nicoSWD\Rules\Core\Methods
 */
final class Split extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenArray
     * @throws \Exception
     */
    public function call(TokenCollection $parameters = \null)
    {
        $parameters->rewind();

        $tokenValue = $this->token->getValue();
        $param = $parameters->current();

        if (!$param || !is_string($param->getValue())) {
            $newValue = [$tokenValue];
        } else {
            if ($param instanceof TokenRegex) {
                $newValue = preg_split($param->getValue(), $tokenValue);
            } else {
                $newValue = explode($param->getValue(), $tokenValue);
            }
        }

        return new TokenArray(
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
