<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Tokenizer;

use ArrayIterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;
use SplPriorityQueue;

final class Tokenizer implements TokenizerInterface
{
    private array $tokens = [];
    private string $compiledRegex = '';

    public function __construct(
        private Grammar $grammar,
        private TokenFactory $tokenFactory
    ) {
        foreach ($grammar->getDefinition() as [$class, $regex, $priority]) {
            $this->registerToken($class, $regex, $priority);
        }
    }

    public function tokenize(string $string): ArrayIterator
    {
        $regex = $this->getRegex();
        $stack = [];
        $offset = 0;

        while (preg_match($regex, $string, $matches, 0, $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $this->tokenFactory->createFromTokenName($token);

            $stack[] = new $className($matches[$token], $offset);
            $offset += strlen($matches[0]);
        }

        return new ArrayIterator($stack);
    }

    public function getGrammar(): Grammar
    {
        return $this->grammar;
    }

    private function registerToken(string $class, string $regex, int $priority): void
    {
        $this->tokens[$class] = new class($class, $regex, $priority) {
            public function __construct(
                public string $class,
                public string $regex,
                public int $priority
            ) {
            }
        };
    }

    private function getMatchedToken(array $matches): string
    {
        foreach ($matches as $key => $value) {
            if ($value !== '' && !is_int($key)) {
                return $key;
            }
        }

        return 'Unknown';
    }

    private function getRegex(): string
    {
        if (!$this->compiledRegex) {
            $regex = [];

            foreach ($this->getQueue() as $token) {
                $regex[] = "(?<$token->class>$token->regex)";
            }

            $this->compiledRegex = '~(' . implode('|', $regex) . ')~As';
        }

        return $this->compiledRegex;
    }

    private function getQueue(): SplPriorityQueue
    {
        $queue = new SplPriorityQueue();

        foreach ($this->tokens as $class) {
            $queue->insert($class, $class->priority);
        }

        return $queue;
    }
}
