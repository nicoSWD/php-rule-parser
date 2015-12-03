<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\{TokenRegex, TokenString};

final class Replace extends CallableFunction
{
    /**
     * @throws \Exception
     */
    public function call(TokenCollection $parameters) : TokenString
    {
        $numParams = $parameters->count();
        $isRegExpr = false;

        if ($numParams < 1) {
            $search = '';
        } else {
            $search = $parameters->current()->getValue();
            $isRegExpr = ($parameters->current() instanceof TokenRegex);
        }

        if ($numParams < 2) {
            $replace = 'undefined';
        } else {
            $parameters->next();
            $replace = $parameters->current()->getValue();
        }

        if ($isRegExpr) {
            list ($expression, $modifiers) = $this->splitRegex($search);

            $modifiers = str_replace('g', '', $modifiers, $count);
            $limit = $count > 0 ? -1 : 1;

            $value = preg_replace(
                $expression . $modifiers,
                $replace,
                $this->token->getValue(),
                $limit
            );
        } else {
            $value = str_replace($search, $replace, $this->token->getValue());
        }

        return new TokenString(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    private function splitRegex(string $regExpr) : array
    {
        preg_match('~(.*?/)([img]{0,3})?$~', $regExpr, $match);

        return [$match[1], $match[2]];
    }

    public function getName() : string
    {
        return 'replace';
    }
}
