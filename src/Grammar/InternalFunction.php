<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MITz<
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar;

final class InternalFunction
{
    public function __construct(
        public readonly string $name,
        public readonly string $class,
    ) {
    }
}
