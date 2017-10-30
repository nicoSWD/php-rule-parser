<?php

declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Grammar;

abstract class Grammar
{
    abstract public function getDefinition(): array;

    public function getInternalFunctions(): array
    {
        return [];
    }

    public function getInternalMethods(): array
    {
        return [];
    }
}
