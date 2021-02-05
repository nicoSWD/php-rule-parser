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
    private const MAGIC_METHOD_PREFIX = '__';

    private TokenFactory $tokenFactory;
    private Closure $callable;
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

    public function call(?BaseToken ...$param): BaseToken
    {
        $callableCopy = $this->callable;

        return $this->tokenFactory->createFromPHPType(
            $callableCopy(...$param)
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
            return fn () => $object->{$methodName};
        }

        $method = $this->findCallableMethod($object, $methodName);

        return fn (?BaseToken ...$params) => $method(
            ...$this->getTokenValues($params)
        );
    }

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    private function findCallableMethod(object $object, string $methodName): callable
    {
        $this->assertNonMagicMethod($methodName);
        $index = 0;

        do {
            if (!isset($this->methodPrefixes[$index])) {
                throw new Exception\UndefinedMethodException();
            }

            $callableMethod = $this->methodPrefixes[$index++] . $methodName;
        } while (!is_callable([$object, $callableMethod]));

        return [$object, $callableMethod];
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
        if (str_starts_with($methodName, self::MAGIC_METHOD_PREFIX)) {
            throw new Exception\ForbiddenMethodException();
        }
    }
}
