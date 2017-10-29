<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Grammar\JavaScript\Methods;

use nicoSWD\Rules\Grammar\CallableFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenString;

final class ToLowerCase extends CallableFunction
{
    /**
     * @param BaseToken $string
     * @return BaseToken
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function call($string = null): BaseToken
    {
        return new TokenString(
            strtolower((string) $this->token->getValue()),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
