<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenBool;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableFunction;

final class Test extends CallableFunction
{
    public function call(mixed ...$parameters): TokenBool
    {
        $pattern = $this->getPattern();

        if ($pattern === null) {
            throw new ParserException('test() is not a function');
        }

        $subject = $this->parseParameter($parameters, numParam: 0);

        if ($subject === null) {
            return TokenBool::fromBool(false);
        }

        while (is_array($subject)) {
            $subject = current($subject);
        }


        $bool = (bool) preg_match($pattern, (string) $subject);

        return TokenBool::fromBool($bool);
    }

    private function getPattern(): ?string
    {
        // When called from RegexNode, $this->token is a BaseToken
        if ($this->token instanceof BaseToken) {
            if (!$this->token->isOfKind(TokenKind::REGEX)) {
                return null;
            }
            $pattern = $this->token->getValue();
        } elseif (!is_string($this->token)) {
            return null;
        } else {
            $pattern = $this->token;
        }

        if (!preg_match('~^/.+/[img]{0,3}$~', $pattern)) {
            return null;
        }

        // Remove "g" modifier as it does not exist in PHP
        // It's also irrelevant in .test() but allowed in JS here
        return preg_replace_callback(
            '~/[igm]{0,3}$~',
            static fn (array $modifiers): string => str_replace('g', '', $modifiers[0]),
            $pattern
        );
    }
}


