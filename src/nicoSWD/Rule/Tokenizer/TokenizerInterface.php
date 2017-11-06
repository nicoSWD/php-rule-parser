<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Tokenizer;

use nicoSWD\Rule\Grammar\Grammar;

interface TokenizerInterface
{
    public function tokenize(string $string): TokenStack;

    public function getGrammar(): Grammar;
}
