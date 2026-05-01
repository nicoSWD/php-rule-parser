<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\Grammar\CallableInterface;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use nicoSWD\Rule\TokenStream\Token\TokenKind;

class MethodRegistry
{
    /** @var array<string, class-string> */
    private array $methods;

    public function __construct(
        private readonly Grammar $grammar,
        private readonly TokenFactory $tokenFactory,
        private readonly ObjectMethodCallerFactoryInterface $userMethodFactory,
    ) {
        $this->methods = [];
        $this->registerMethods();
    }

    /**
     * @throws Exception\UndefinedMethodException
     * @throws Exception\ForbiddenMethodException
     */
    public function get(string $methodName, BaseToken $token, mixed $rawValue = null): CallableInterface
    {
        if ($token->isOfKind(TokenKind::OBJECT)) {
            return $this->getObjectMethodCaller($token, $methodName);
        }

        if (!isset($this->methods[$methodName])) {
            throw new Exception\UndefinedMethodException();
        }

        // For regex tokens, pass the original token to preserve type information
        if ($token->isOfKind(TokenKind::REGEX)) {
            return new $this->methods[$methodName]($token);
        }

        // Pass the raw PHP value directly to the callable
        return new $this->methods[$methodName]($rawValue);
    }

    private function registerMethods(): void
    {
        foreach ($this->grammar->getInternalMethods() as $internalCallable) {
            $this->methods[$internalCallable->name] = $internalCallable->class;
        }
    }

    /**
     * @throws Exception\ForbiddenMethodException
     * @throws Exception\UndefinedMethodException
     */
    private function getObjectMethodCaller(BaseToken $token, string $methodName): CallableInterface
    {
        return $this->userMethodFactory->create($token, $this->tokenFactory, $methodName);
    }
}
