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
use nicoSWD\Rules\Exceptions\ParserException;
use nicoSWD\Rules\Tokens\{TokenBool, TokenRegex};
use nicoSWD\Rules\Tokens\BaseToken;

final class Test extends CallableFunction
{
    /**
     * @param BaseToken $string
     * @return TokenBool
     * @throws ParserException
     */
    public function call($string = null) : TokenBool
    {
        if (!$this->token instanceof TokenRegex) {
            throw new ParserException(sprintf(
                'undefined is not a function at position %d on line %d',
                $this->token->getPosition(),
                $this->token->getLine()
            ));
        }

        if (!$string) {
            $bool = false;
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

            $subject = $string->getValue();

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

    public function getName() : string
    {
        return 'test';
    }
}
