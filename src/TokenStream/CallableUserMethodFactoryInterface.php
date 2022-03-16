<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

interface CallableUserMethodFactoryInterface
{
    /**
     * @throws Exception\ForbiddenMethodException
     * @throws Exception\UndefinedMethodException
     */
    public function create(BaseToken $token, TokenFactory $tokenFactory, string $methodName): CallableUserFunctionInterface;
}
