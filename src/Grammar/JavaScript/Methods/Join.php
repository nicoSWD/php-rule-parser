<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar\JavaScript\Methods;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\GenericToken;
use nicoSWD\Rule\TokenStream\Token\TokenKind;
use nicoSWD\Rule\TokenStream\TokenCollection;
use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\Grammar\CallableFunction;

final class Join extends CallableFunction
{
    public function call(?BaseToken ...$parameters): BaseToken
    {
        if (!$this->token->isOfKind(TokenKind::ARRAY)) {
            throw new ParserException(sprintf('%s.join is not a function', $this->token->getValue()));
        }

        $glue = $this->parseParameter($parameters, numParam: 0);

        if ($glue) {
            $glue = $glue->getValue();
        } else {
            $glue = ',';
        }

        $array = $this->token->getValue();

        if ($array instanceof TokenCollection) {
            $array = $array->toArray();
        }

        return new GenericToken(TokenKind::STRING, implode($glue, $array));
    }
}
