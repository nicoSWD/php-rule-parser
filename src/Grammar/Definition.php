<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Grammar;

use nicoSWD\Rule\TokenStream\Token\Token;

final class Definition
{
    public function __construct(
        public readonly Token $token,
        public readonly string $regex,
        public readonly int $priority,
    ) {
    }
}
