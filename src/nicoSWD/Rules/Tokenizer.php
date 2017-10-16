<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules;

use SplPriorityQueue;
use stdClass;

final class Tokenizer implements TokenizerInterface
{
    private $internalTokens = [];

    private $regex = '';

    private $regexRequiresReassembly = false;

    public function __construct(Grammar $grammar)
    {
        foreach ($grammar->getDefinition() as list($class, $regex, $priority)) {
            $this->registerToken($class, $regex, $priority);
        }
    }

    public function tokenize(string $string): Stack
    {
        $stack = new Stack();
        $regex = $this->getRegex();
        $baseNameSpace = __NAMESPACE__ . '\\Tokens\\Token';
        $offset = 0;

        while (preg_match($regex, $string, $matches, 0, $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $baseNameSpace . $token;

            $stack->attach(new $className(
                $matches[$token],
                $offset,
                $stack
            ));

            $offset += strlen($matches[0]);
        }

        return $stack;
    }

    public function registerToken(string $class, string $regex, int $priority = null)
    {
        $token = new stdClass();
        $token->class = $class;
        $token->regex = $regex;
        $token->priority = $priority ?? $this->getPriority($class);

        $this->internalTokens[$class] = $token;
        $this->regexRequiresReassembly = true;
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
        if (!$this->regex || $this->regexRequiresReassembly) {
            $regex = [];

            foreach ($this->getQueue() as $token) {
                $regex[] = "(?<$token->class>$token->regex)";
            }

            $this->regex = sprintf('~(%s)~As', implode('|', $regex));
            $this->regexRequiresReassembly = false;
        }

        return $this->regex;
    }

    private function getQueue(): SplPriorityQueue
    {
        $queue = new SplPriorityQueue();

        foreach ($this->internalTokens as $class) {
            $queue->insert($class, $class->priority);
        }

        return $queue;
    }

    private function getPriority(string $class): int
    {
        return $this->internalTokens[$class]->priority ?? 10;
    }
}
