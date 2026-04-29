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

final class Concat extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $value = $this->token->getValue();

        foreach ($parameters as $parameter) {
            if ($parameter->isOfKind(TokenKind::ARRAY)) {
                $value .= implode(',', $parameter->toArray());
            } else {
                $value .= $parameter->getValue();
            }
        }

        return new GenericToken(TokenKind::STRING, $value);
    }
}
