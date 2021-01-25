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
use nicoSWD\Rule\TokenStream\Token\TokenInteger;
use nicoSWD\Rule\TokenStream\Token\TokenString;

final class StartsWith extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        if (!$this->token instanceof TokenString) {
            throw new ParserException('Call to undefined method "startsWith" on non-string');
        }

        $needle = $this->parseParameter($parameters, numParam: 0);
        $offset = $this->getOffset($this->parseParameter($parameters, numParam: 1));
        $position = strpos($this->token->getValue(), $needle->getValue(), $offset);

        return TokenBool::fromBool($position === $offset);
    }

    private function getOffset(?BaseToken $offset): int
    {
        if ($offset instanceof TokenInteger) {
            $offset = $offset->getValue();
        } else {
            $offset = 0;
        }

        return $offset;
    }
}
