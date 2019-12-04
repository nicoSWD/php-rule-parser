<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class CallableUserMethodFactory implements CallableUserMethodFactoryInterface
{
    public function create(BaseToken $token, TokenFactory $tokenFactory, string $methodName): CallableUserMethod
    {
        return new CallableUserMethod($token, $tokenFactory, $methodName);
    }
}
