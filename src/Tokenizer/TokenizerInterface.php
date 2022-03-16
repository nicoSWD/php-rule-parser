<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Tokenizer;

use Iterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

abstract class TokenizerInterface
{
    public readonly Grammar $grammar;

    /**
     * @param string $string
     * @return Iterator<int, BaseToken>
     */
    abstract public function tokenize(string $string): Iterator;
}
