<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

final class IntegerNode extends ValueNode
{
    public function __construct(
        public readonly int $value,
    ) {
    }

    public function getNativeValue(): int
    {
        return $this->value;
    }
}
