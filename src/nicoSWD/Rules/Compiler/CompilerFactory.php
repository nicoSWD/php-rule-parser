<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Compiler;

class CompilerFactory implements CompilerFactoryInterface
{
    public function create(): CompilerInterface
    {
        return new StandardCompiler();
    }
}
