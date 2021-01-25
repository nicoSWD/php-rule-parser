<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\Grammar\CallableFunction;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenBool;
use nicoSWD\Rule\TokenStream\Token\TokenString;

final class EndsWith extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        if (!$this->token instanceof TokenString) {
            throw new ParserException('Call to undefined method "endsWith" on non-string');
        }

        $needle = $this->parseParameter($parameters, numParam: 0);
        $haystack = $this->token->getValue();

        if (!$needle) {
            $result = false;
        } else {
            $needle = $needle->getValue();
            $result = str_ends_with($haystack, $needle);
        }

        return TokenBool::fromBool($result);
    }
}
