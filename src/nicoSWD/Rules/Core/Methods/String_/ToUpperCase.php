<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3.4
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Core\Methods\String_;

use nicoSWD\Rules\Core\Methods\CallableMethod;
use nicoSWD\Rules\Tokens\TokenString;

/**
 * Class ToUpperCase
 * @package nicoSWD\Rules\Core\Methods\String_
 */
final class ToUpperCase extends CallableMethod
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param mixed[] $parameters
     * @return TokenString
     * @throws \Exception
     */
    public function call(array $parameters = [])
    {
        return new TokenString(
            '"' . strtoupper($this->token->getValue()) . '"',
            $this->token->getOffset(),
            $this->token->getStack()
        );
    }
}
