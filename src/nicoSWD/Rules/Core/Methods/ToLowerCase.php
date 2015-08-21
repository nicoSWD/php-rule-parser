<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods;

use nicoSWD\Rules\AST\TokenCollection;
use nicoSWD\Rules\Core\CallableFunction;
use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class ToLowerCase
 * @package nicoSWD\Rules\Core\Methods
 */
final class ToLowerCase extends CallableFunction
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param TokenCollection $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(TokenCollection $parameters)
    {
        return new TokenString(
            strtolower($this->token->getValue()),
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'toLowerCase';
    }
}
