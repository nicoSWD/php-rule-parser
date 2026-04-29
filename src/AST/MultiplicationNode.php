<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <hello@nico.es>
 */
namespace nicoSWD\Rule\AST;

final class MultiplicationNode extends Node
{
    public function __construct(
        public Node $left,
        public Node $right,
        public int $offset = 0,
    ) {
    }
}
