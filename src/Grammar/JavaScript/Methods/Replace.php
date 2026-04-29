<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class Replace extends CallableFunction
{
    public function call(mixed ...$parameters): GenericToken
    {
        $search = $this->parseParameter($parameters, numParam: 0);

        if ($search === null || $search === '') {
            $search = '';
            $isRegExpr = false;
        } else {
            $isRegExpr = $this->isRegex($search);
        }

        $replace = $this->parseParameter($parameters, numParam: 1);

        if ($replace === null) {
            $replace = 'undefined';
        }

        if ($isRegExpr) {
            $value = $this->doRegexReplace($search, $replace);
        } else {
            $value = str_replace($search, $replace, $this->token);
        }

        return new GenericToken(TokenKind::STRING, $value);
    }

    private function isRegex(string $value): bool
    {
        return (bool) preg_match('~^/.+/[img]{0,3}$~', $value);
    }

    private function doRegexReplace(string $search, string $replace): string
    {
        [$expression, $modifiers] = $this->splitRegex($search);

        $modifiers = str_replace('g', '', $modifiers, $count);
        $limit = $count > 0 ? -1 : 1;

        return preg_replace(
            $expression . $modifiers,
            $replace,
            $this->token,
            $limit
        );
    }

    private function splitRegex(string $regExpr): array
    {
        preg_match('~(?<regex>.*?/)(?<modifiers>[img]{0,3})?$~', $regExpr, $match);

        return [$match['regex'], $match['modifiers']];
    }
}
