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
use stdClass;

final class Tokenizer implements TokenizerInterface
{
    /** @var TokenFactory */
    private $tokenFactory;
    /** @var Grammar */
    private $grammar;
    /** @var stdClass[] */
    private $tokens = [];
    /** @var string */
    private $regex = '';

    public function __construct(Grammar $grammar, TokenFactory $tokenFactory)
    {
        $this->tokenFactory = $tokenFactory;
        $this->grammar = $grammar;

        foreach ($grammar->getDefinition() as list($class, $regex, $priority)) {
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
        $token = new stdClass();
        $token->class = $class;
        $token->regex = $regex;
        $token->priority = $priority;

        $this->tokens[$class] = $token;
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

        foreach ($this->tokens as $class) {
            $queue->insert($class, $class->priority);
        }

        return $queue;
    }
}
