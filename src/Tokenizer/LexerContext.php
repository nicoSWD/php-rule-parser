<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Tokenizer;

/**
 * Internal value object that holds the mutable scanning state for the Lexer.
 *
 * By encapsulating the position, input string, and length in a separate object,
 * the Lexer itself remains stateless and reentrant — multiple calls to tokenize()
 * on the same Lexer instance will not interfere with each other.
 *
 * @internal
 */
final class LexerContext
{
    public function __construct(
        public string $input,
        public int $pos = 0,
        public int $length = 0,
    ) {
        $this->length = strlen($input);
    }

    /**
     * Peek at a character ahead of the current position without advancing.
     */
    public function peek(int $offset = 0): string
    {
        $index = $this->pos + $offset + 1;

        return $index < $this->length ? $this->input[$index] : '';
    }

    /**
     * Get the current character.
     */
    public function current(): string
    {
        return $this->input[$this->pos] ?? '';
    }

    /**
     * Check if the current position is within bounds.
     */
    public function isValid(): bool
    {
        return $this->pos < $this->length;
    }

    /**
     * Check if the remaining input starts with the given string.
     */
    public function startsWith(string $needle): bool
    {
        $len = strlen($needle);

        if ($this->pos + $len > $this->length) {
            return false;
        }

        return substr($this->input, $this->pos, $len) === $needle;
    }
}
