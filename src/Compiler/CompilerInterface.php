<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Compiler;

use nicoSWD\Rule\TokenStream\Token\BaseToken;

interface CompilerInterface
{
    public function getCompiledRule(): string;

    public function addParentheses(BaseToken $token): void;

    public function addLogical(BaseToken $token): void;

    public function addBoolean(bool $bool): void;
}
