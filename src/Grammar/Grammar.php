<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar;

use SplPriorityQueue;

abstract class Grammar
{
    private string $compiledRegex = '';
    /** @var Definition[] */
    private array $tokens = [];

    /** @return Definition[] */
    abstract public function getDefinition(): array;

    /** @return InternalFunction[] */
    abstract public function getInternalFunctions(): array;

    /** @return InternalMethod[] */
    abstract public function getInternalMethods(): array;

    public function buildRegex(): string
    {
        if (!$this->compiledRegex) {
            $this->registerTokens();
            $regex = [];

            foreach ($this->getQueue() as $token) {
                $regex[] = "(?<{$token->token->value}>{$token->regex})";
            }

            $this->compiledRegex = '~(' . implode('|', $regex) . ')~As';
        }

        return $this->compiledRegex;
    }

    private function getQueue(): SplPriorityQueue
    {
        $queue = new SplPriorityQueue();

        foreach ($this->tokens as $token) {
            $queue->insert($token, $token->priority);
        }

        return $queue;
    }

    private function registerTokens(): void
    {
        foreach ($this->getDefinition() as $definition) {
            $this->tokens[$definition->token->value] = $definition;
        }
    }
}
