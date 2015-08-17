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
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens;

/**
 * Class Join
 * @package nicoSWD\Rules\Core\Methods
 */
final class Join extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return Tokens\TokenString
     * @throws ParserException
     */
    public function call(TokenCollection $parameters = \null)
    {
        $parameters->rewind();

        if ($firstParam = $parameters->current()) {
            $glue = $firstParam->getValue();
        } else {
            $glue = ',';
        }

        if (!$array = $this->token->getValue()) {
            $array = [];
        }

        if ($array instanceof TokenCollection) {
            $array = $array->toArray();
        }

        return new Tokens\TokenString(
            implode($glue, $array),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'join';
    }
}
