<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenString;

final class ToUpperCase extends CallableFunction
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function call(TokenCollection $parameters) : TokenString
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
