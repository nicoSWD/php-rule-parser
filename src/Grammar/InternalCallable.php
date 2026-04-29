<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\Grammar;

final readonly class InternalCallable
{
    public function __construct(
        public string $name,
        public string $class,
    ) {
    }
}
