<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenBool;
use nicoSWD\Rule\TokenStream\Token\TokenRegex;
use nicoSWD\Rule\TokenStream\TokenCollection;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableFunction;

final class Test extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        if (!$this->token instanceof TokenRegex) {
            throw new ParserException('undefined is not a function');
        }

        $string = $this->parseParameter($parameters, numParam: 0);

        if (!$string) {
            $bool = false;
        } else {
            // Remove "g" modifier as is does not exist in PHP
            // It's also irrelevant in .test() but allowed in JS here
            $pattern = preg_replace_callback(
                '~/[igm]{0,3}$~',
                fn (array $modifiers) => str_replace('g', '', $modifiers[0]),
                $this->token->getValue()
            );

            $subject = $string->getValue();

            while ($subject instanceof TokenCollection) {
                $subject = current($subject->toArray());
            }

            $bool = (bool) preg_match($pattern, (string) $subject);
        }

        return TokenBool::fromBool($bool);
    }
}
