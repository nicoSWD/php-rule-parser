<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class ObjectMethodCallerFactory implements ObjectMethodCallerFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create(BaseToken $token, TokenFactory $tokenFactory, string $methodName): CallableInterface
    {
        return new ObjectMethodCaller($token, $tokenFactory, $methodName);
    }
}
