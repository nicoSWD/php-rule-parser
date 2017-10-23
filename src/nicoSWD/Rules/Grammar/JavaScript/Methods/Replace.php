<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Grammar\JavaScript\Methods;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenRegex;
use nicoSWD\Rules\Tokens\TokenString;
use nicoSWD\Rules\Tokens\BaseToken;

final class Replace extends CallableFunction
{
    /**
     * @param BaseToken $search
     * @param BaseToken $replace
     * @return BaseToken
     * @throws \Exception
     */
    public function call($search = null, $replace = null): BaseToken
    {
        $isRegExpr = false;

        if (!$search) {
            $search = '';
        } else {
            $isRegExpr = ($search instanceof TokenRegex);
            $search = $search->getValue();
        }

        if (!$replace) {
            $replace = 'undefined';
        } else {
            $replace = $replace->getValue();
        }

        if ($isRegExpr) {
            $value = $this->doRegexReplace($search, $replace);
        } else {
            $value = str_replace($search, $replace, $this->token->getValue());
        }

        return new TokenString(
            $value,
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    private function doRegexReplace($search, $replace)
    {
        list ($expression, $modifiers) = $this->splitRegex($search);

        $modifiers = str_replace('g', '', $modifiers, $count);
        $limit = $count > 0 ? -1 : 1;

        return preg_replace(
            $expression . $modifiers,
            $replace,
            $this->token->getValue(),
            $limit
        );
    }

    private function splitRegex(string $regExpr): array
    {
        preg_match('~(.*?/)([img]{0,3})?$~', $regExpr, $match);

        return [$match[1], $match[2]];
    }

    public function getName(): string
    {
        return 'replace';
    }
}
