<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\BaseToken;
use nicoSWD\Rules\Tokens\TokenString;

final class ToUpperCase extends CallableFunction
{
    /**
     * @param BaseToken $string
     * @return TokenString
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function call($string = null) : TokenString
    {
        return new TokenString(
            strtoupper((string) $this->token->getValue()),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    public function getName() : string
    {
        return 'toUpperCase';
    }
}
