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
     * @param string $string
     * @return Stack
     * @throws \Exception
     */
    public function tokenize($string);

    /**
     * @param string $class
     * @param string $regex
     * @param int    $priority
     */
    public function registerToken($class, $regex, $priority = null);
}
