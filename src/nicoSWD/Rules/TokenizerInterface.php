<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
declare(strict_types=1);

namespace nicoSWD\Rules;

interface TokenizerInterface
{
    /**
     * @throws \Exception
     */
    public function tokenize(string $string) : Stack;

    public function registerToken(string $token, string $regex, int $priority);
}
