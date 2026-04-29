<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class CharAt extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $tokenValue = $this->token->getValue();
        $offset = $this->parseParameter($parameters, numParam: 0);

        if (!$offset) {
            $offset = 0;
        } else {
            $offset = (int) $offset->getValue();
        }

        $char = $tokenValue[$offset] ?? '';

        return new GenericToken(TokenKind::STRING, $char);
    }
}
