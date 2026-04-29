<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Functions;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

final class ParseInt extends CallableFunction implements CallableInterface
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        $value = $this->parseParameter($parameters, numParam: 0);

        if (!isset($value)) {
            return new GenericToken(TokenKind::FLOAT, NAN);
        }

        return new GenericToken(TokenKind::INTEGER, (int) $value->getValue());
    }
}
