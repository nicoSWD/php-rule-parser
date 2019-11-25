<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\TokenStream;

use ArrayIterator;

class TokenStreamFactory
{
    public function create(ArrayIterator $stack, AST $ast): TokenStream
    {
        return new TokenStream($stack, $ast);
    }
}
