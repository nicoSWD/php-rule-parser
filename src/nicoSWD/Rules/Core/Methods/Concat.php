<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Tokens;
use nicoSWD\Rules\Core\CallableFunction;

/**
 * Class Concat
 * @package nicoSWD\Rules\Core\Methods
 */
final class Concat extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return Tokens\TokenString
     */
    public function call(TokenCollection $parameters)
    {
        $value = $this->token->getValue();

        foreach ($parameters as $parameter) {
            if ($parameter instanceof Tokens\TokenArray) {
                $value .= implode(',', $parameter->toArray());
            } else {
                $value .= $parameter->getValue();
            }
        }

        return new Tokens\TokenString(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'concat';
    }
}
