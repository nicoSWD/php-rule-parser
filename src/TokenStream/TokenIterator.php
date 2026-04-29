<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\TokenStream;

use Iterator;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

readonly class TokenIterator implements Iterator
{
    public function __construct(
        private Iterator $stack,
    ) {
    }

    public function next(): void
    {
        $this->stack->next();
    }

    public function valid(): bool
    {
        return $this->stack->valid();
    }

    public function current(): BaseToken
    {
        return $this->getCurrentToken();
    }

    /**
     * Returns the raw token without creating a node.
     * Used by the AST parser to inspect tokens without resolving them.
     */
    public function peekRaw(): BaseToken
    {
        return $this->getCurrentToken();
    }

    public function key(): int
    {
        return $this->stack->key();
    }

    public function rewind(): void
    {
        $this->stack->rewind();
    }

    private function getCurrentToken(): BaseToken
    {
        return $this->stack->current();
    }
}
