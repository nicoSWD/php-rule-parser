<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.5
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\TokenBool;
use nicoSWD\Rules\Tokens\TokenRegex;
use nicoSWD\Rules\Core\CallableFunction;

/**
 * Class Test
 * @package nicoSWD\Rules\Core\Methods
 */
final class Test extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenBool
     * @throws ParserException
     */
    public function call(TokenCollection $parameters)
    {
        if (!$this->token instanceof TokenRegex) {
            throw new ParserException(sprintf(
                'undefined is not a function at position %d on line %d',
                $this->token->getPosition(),
                $this->token->getLine()
            ));
        }

        $parameters->rewind();

        if ($parameters->count() < 1) {
            $bool = \false;
        } else {
            // Remove "g" modifier as is does not exist in PHP
            // It's also irrelevant in .test() but allowed in JS here
            $pattern = preg_replace_callback(
                '~/[igm]{0,3}$~',
                function (array $modifiers) {
                    return str_replace('g', '', $modifiers[0]);
                },
                $this->token->getValue()
            );

            $subject = $parameters->current()->getValue();

            while ($subject instanceof TokenCollection) {
                $subject = current($subject->toArray());
            }

            $bool = (bool) preg_match($pattern, (string) $subject);
        }

        return new TokenBool(
            $bool,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'test';
    }
}
