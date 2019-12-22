<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Tokenizer;

use ArrayIterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

interface TokenizerInterface
{
    /** @return ArrayIterator<int, BaseToken> */
    public function tokenize(string $string): ArrayIterator;

    public function getGrammar(): Grammar;
}
