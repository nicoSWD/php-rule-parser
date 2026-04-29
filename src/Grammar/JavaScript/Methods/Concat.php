<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class Concat extends CallableFunction
{
    public function call(mixed ...$parameters): GenericToken
    {
        $value = $this->token;

        foreach ($parameters as $parameter) {
            if (is_array($parameter)) {
                $value .= implode(',', $parameter);
            } else {
                $value .= $parameter;
            }
        }

        return new GenericToken(TokenKind::STRING, $value);
    }
}
