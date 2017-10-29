<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Tokenizer;

use nicoSWD\Rules\Grammar\Grammar;

interface TokenizerInterface
{
    public function tokenize(string $string): TokenStack;

    public function getGrammar(): Grammar;
}
