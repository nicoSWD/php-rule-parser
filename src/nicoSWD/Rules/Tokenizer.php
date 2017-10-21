<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

use nicoSWD\Rules\Tokens\TokenFactory;
use SplPriorityQueue;
use stdClass;

final class Tokenizer implements TokenizerInterface
{
    /** @var TokenFactory */
    private $tokenFactory;

    private $internalTokens = [];

    private $regex = '';

    public function __construct(Grammar $grammar, TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;

        foreach ($grammar->getDefinition() as list($class, $regex, $priority)) {
            $this->registerToken($class, $regex, $priority);
        }
    }

    public function tokenize(string $string): Stack
    {
        $stack = new Stack();
        $regex = $this->getRegex();
        $offset = 0;

        while (preg_match($regex, $string, $matches, 0, $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $this->tokenFactory->createFromTokenName($token);

            $stack->attach(new $className(
                $matches[$token],
                $offset,
                $stack
            ));

            $offset += strlen($matches[0]);
        }

        return $stack;
    }

    private function registerToken(string $class, string $regex, int $priority)
    {
        $token = new stdClass();
        $token->class = $class;
        $token->regex = $regex;
        $token->priority = $priority;

        $this->internalTokens[$class] = $token;
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
        if (!$this->regex) {
            $regex = [];

            foreach ($this->getQueue() as $token) {
                $regex[] = "(?<$token->class>$token->regex)";
            }

            $this->regex = '~(' . implode('|', $regex) . ')~As';
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
}
