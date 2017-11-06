<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules\Compiler;

use nicoSWD\Rules\TokenStream\Token\BaseToken;

interface CompilerInterface
{
    public function getCompiledRule(): string;

    public function addParentheses(BaseToken $token);

    public function addLogical(BaseToken $token);

    public function addBoolean(bool $bool);
}
