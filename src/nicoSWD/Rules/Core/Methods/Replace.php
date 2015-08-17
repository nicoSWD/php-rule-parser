<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Tokens\TokenRegex;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\Core\CallableFunction;

/**
 * Class Replace
 * @package nicoSWD\Rules\Core\Methods
 */
final class Replace extends CallableFunction
{
    /**
     * @param TokenCollection $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(TokenCollection $parameters = \null)
    {
        $parameters->rewind();
        $numParams = $parameters->count();
        $isRegExpr = \false;

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

    /**
     * @return string
     */
    public function getName()
    {
        return 'replace';
    }

    /**
     * @internal
     * @param string $regExpr
     * @return array
     */
    private function splitRegex($regExpr)
    {
        preg_match('~(.*?/)([img]{0,3})?$~', $regExpr, $match);

        return [$match[1], $match[2]];
    }
}
