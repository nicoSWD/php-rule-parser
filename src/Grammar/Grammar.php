<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar;

abstract class Grammar
{
    /** @return array<int, <mixed>> */
    abstract public function getDefinition(): array;

    /** @return array<string, string> */
    public function getInternalFunctions(): array
    {
        return [];
    }

    /** @return array<string, string> */
    public function getInternalMethods(): array
    {
        return [];
    }
}
