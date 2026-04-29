<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\TokenStream;

use nicoSWD\Rule\Parser\Exception\ParserException;
use nicoSWD\Rule\TokenStream\Exception\UndefinedVariableException;
use nicoSWD\Rule\TokenStream\Token\BaseToken;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

class VariableRegistry
{
    /** @param array<string, mixed> $variables */
    public function __construct(
        private array $variables,
        private readonly TokenFactory $tokenFactory,
    ) {
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * @throws UndefinedVariableException
     * @throws ParserException
     */
    public function get(string $variableName): BaseToken
    {
        if (!$this->exists($variableName)) {
            throw new UndefinedVariableException($variableName);
        }

        return $this->tokenFactory->createFromPHPType($this->variables[$variableName]);
    }

    public function exists(string $variableName): bool
    {
        return array_key_exists($variableName, $this->variables);
    }
}
