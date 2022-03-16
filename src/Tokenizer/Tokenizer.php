<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Tokenizer;

use ArrayIterator;
use Iterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\Token;
use nicoSWD\Rule\TokenStream\Token\TokenFactory;

final class Tokenizer extends TokenizerInterface
{
    public function __construct(
        public readonly Grammar $grammar,
        private readonly TokenFactory $tokenFactory,
    ) {
    }

    public function tokenize(string $string): Iterator
    {
        $regex = $this->grammar->buildRegex();
        $stack = [];
        $offset = 0;

        while (preg_match($regex, $string, $matches, offset: $offset)) {
            $token = $this->getMatchedToken($matches);
            $className = $this->tokenFactory->createFromToken($token);

            $stack[] = new $className($matches[$token->value], $offset);
            $offset += strlen($matches[0]);
        }

        return new ArrayIterator($stack);
    }

    private function getMatchedToken(array $matches): Token
    {
        foreach ($matches as $key => $value) {
            if ($value !== '' && !is_int($key)) {
                return Token::from($key);
            }
        }

        return Token::UNKNOWN;
    }
}
