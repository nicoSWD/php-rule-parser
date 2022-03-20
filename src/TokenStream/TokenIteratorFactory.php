<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use Iterator;

final class TokenIteratorFactory
{
    public function create(Iterator $stack, TokenStream $tokenStream): TokenIterator
    {
        return new TokenIterator($stack, $tokenStream);
    }
}
