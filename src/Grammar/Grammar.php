<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar;

abstract class Grammar
{
    /** @return InternalFunction[] */
    abstract public function getInternalFunctions(): array;

    /** @return InternalMethod[] */
    abstract public function getInternalMethods(): array;
}
