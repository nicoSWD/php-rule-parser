<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream;

use Closure;
use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class ObjectMethodCaller implements CallableInterface
{
    private const string MAGIC_METHOD_PREFIX = '__';
    private readonly TokenFactory $tokenFactory;
    private readonly Closure $callable;
    private array $methodPrefixes = ['', 'get', 'is', 'get_', 'is_'];

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    public function __construct(BaseToken $token, TokenFactory $tokenFactory, string $methodName)
    {
        $this->tokenFactory = $tokenFactory;
        $this->callable = $this->getCallable($token, $methodName);
    }

    public function call(mixed ...$param): BaseToken
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
    private function getCallable(BaseToken $token, string $methodName): Closure
    {
        $object = $token->getValue();

        if (property_exists($object, $methodName)) {
            return static fn (): mixed => $object->{$methodName};
        }

        $method = $this->findCallableMethod($object, $methodName);

        return fn (mixed ...$params): mixed => $method(...$params);
    }

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    private function findCallableMethod(object $object, string $methodName): callable
    {
        $this->assertNonMagicMethod($methodName);

        // Check exact method name first
        if (is_callable([$object, $methodName])) {
            return [$object, $methodName];
        }

        // Try prefixed versions (get, is, get_, is_)
        foreach ($this->methodPrefixes as $prefix) {
            if ($prefix === '') {
                continue;
            }

            $prefixedMethod = $prefix . $methodName;

            if (is_callable([$object, $prefixedMethod])) {
                return [$object, $prefixedMethod];
            }
        }

        throw new Exception\UndefinedMethodException();
    }

    /** @throws Exception\ForbiddenMethodException */
    private function assertNonMagicMethod(string $methodName): void
    {
        if (str_starts_with($methodName, self::MAGIC_METHOD_PREFIX)) {
            throw new Exception\ForbiddenMethodException();
        }
    }
}
