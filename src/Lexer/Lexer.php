<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Lexer;

use Iterator;
use nicoSWD\Rule\Grammar\Grammar;
use nicoSWD\Rule\TokenStream\Token\BaseToken;

abstract class Lexer
{
    protected Grammar $grammar;

    /**
     * @param string $string
     * @return Iterator<int, BaseToken>
     */
    abstract public function tokenize(string $string): Iterator;
}
