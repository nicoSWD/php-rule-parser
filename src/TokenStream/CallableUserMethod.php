<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\Grammar\CallableUserFunctionInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class CallableUserMethod implements CallableUserFunctionInterface
{
    /** @var TokenFactory */
    private $tokenFactory;
    /** @var callable */
    private $callable;
    /** @var string[] */
    private $methodPrefixes = ['get', 'is', '', 'get_', 'is_'];

    public function __construct(BaseToken $token, TokenFactory $tokenFactory, string $methodName)
    {
        $this->tokenFactory = $tokenFactory;
        $this->callable = $this->getCallable($token, $methodName);
    }

    public function call(BaseToken $param = null): BaseToken
    {
        $callable = $this->callable;

        return $this->tokenFactory->createFromPHPType(
            $callable($param)
        );
    }

    private function getCallable(BaseToken $token, string $methodName): callable
    {
        $object = $token->getValue();

        if (property_exists($object, $methodName)) {
            return function () use ($object, $methodName) {
                return $object->{$methodName};
            };
        }

        $method = $this->findCallableMethod($object, $methodName);

        return function (BaseToken $param = null) use ($method) {
            if ($param !== null) {
                return $method($param->getValue());
            }

            return $method();
        };
    }

    private function findCallableMethod($object, string $methodName): callable
    {
        $callable = [$object, $methodName];
        $index = 0;

        do {
            if (!isset($this->methodPrefixes[$index])) {
                throw new Exception\UndefinedMethodException();
            }

            $callable[1] = $this->methodPrefixes[$index++] . $methodName;
        } while (!is_callable($callable));

        return $callable;
    }
}
