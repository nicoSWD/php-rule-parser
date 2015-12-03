<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @since       0.3
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rules;

/**
 * Interface TokenizerInterface
 * @package nicoSWD\Rules
 */
interface TokenizerInterface
{
    /**
     * @throws \Exception
     */
    public function tokenize(string $string) : Stack;
}
