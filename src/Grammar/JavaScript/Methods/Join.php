<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableFunction;

final class Join extends CallableFunction
{
    public function call(mixed ...$parameters): GenericToken
    {
        if (!is_array($this->token)) {
            throw new ParserException(sprintf('%s.join is not a function', $this->token));
        }

        $glue = $this->parseParameter($parameters, numParam: 0);

        if ($glue !== null) {
            $glue = (string) $glue;
        } else {
            $glue = ',';
        }

        return new GenericToken(TokenKind::STRING, implode($glue, $this->token));
    }
}
