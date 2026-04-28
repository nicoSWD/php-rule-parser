<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

final class MethodCallNode extends Node
{
    /** @param Node[] $arguments */
    public function __construct(
        public readonly Node $object,
        public readonly string $name,
        public readonly array $arguments,
        public readonly int $offset = 0,
    ) {
    }
}
