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
    const MAGIC_METHOD_PREFIX = '__';

    /** @var TokenFactory */
    private $tokenFactory;
    /** @var callable */
    private $callable;
    /** @var string[] */
    private $methodPrefixes = ['', 'get', 'is', 'get_', 'is_'];

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    public function __construct(BaseToken $token, TokenFactory $tokenFactory, string $methodName)
    {
        $this->tokenFactory = $tokenFactory;
        $this->callable = $this->getCallable($token, $methodName);
    }

    public function call(?BaseToken ...$param): BaseToken
    {
        $callable = $this->callable;

        return $this->tokenFactory->createFromPHPType(
            $callable(...$param)
        );
    }

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    private function getCallable(BaseToken $token, string $methodName): callable
    {
        $object = $token->getValue();

        if (property_exists($object, $methodName)) {
            return function () use ($object, $methodName) {
                return $object->{$methodName};
            };
        }

        $method = $this->findCallableMethod($object, $methodName);

        return function (?BaseToken ...$params) use ($method) {
            return $method(...$this->getTokenValues($params));
        };
    }

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    private function findCallableMethod($object, string $methodName): callable
    {
        $this->assertNonMagicMethod($methodName);

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

    private function getTokenValues(array $params): array
    {
        $values = [];

        foreach ($params as $token) {
            $values[] = $token->getValue();
        }

        return $values;
    }

    /** @throws Exception\ForbiddenMethodException */
    private function assertNonMagicMethod(string $methodName): void
    {
        if (substr($methodName, 0, 2) === self::MAGIC_METHOD_PREFIX) {
            throw new Exception\ForbiddenMethodException();
        }
    }
}
