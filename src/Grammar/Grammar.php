<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */

declare(strict_types=1);

namespace nicoSWD\Rule\Grammar;

abstract class Grammar
{
    /** @return InternalCallable[] */
    abstract public function getInternalFunctions(): array;

    /** @return InternalCallable[] */
    abstract public function getInternalMethods(): array;
}
