<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use Closure;
use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class CallableUserMethod implements CallableUserFunctionInterface
{
    /** @var TokenFactory */
    private $tokenFactory;
    /** @var Closure */
    private $callable;
    /** @var string[] */
    private $methodPrefixes = ['get', 'is', '', 'get_', 'is_'];

    public function __construct(BaseToken $token, TokenFactory $tokenFactory, string $methodName)
    {
        $this->tokenFactory = $tokenFactory;
        $this->callable = $this->getCallable($token, $methodName);
    }

    private function getCallable(BaseToken $token, string $methodName): Closure
    {
        $object = $token->getValue();

        if (property_exists($object, $methodName)) {
            return function () use ($object, $methodName) {
                return $object->{$methodName};
            };
        }

        $method = [$object];
        $index = 0;

        do {
            if (!isset($this->methodPrefixes[$index])) {
                throw new Exception\UndefinedMethodException();
            }

            $method[1] = $this->methodPrefixes[$index++] . $methodName;
        } while (!is_callable($method));

        return function (BaseToken $param = null) use ($method) {
            return $method($param ? $param->getValue() : null);
        };
    }

    public function call(BaseToken $param = null): BaseToken
    {
        $callable = $this->callable;

        return $this->tokenFactory->createFromPHPType(
            $callable($param)
        );
    }
}
