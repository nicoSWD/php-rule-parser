<?php

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 *
 * @link        https://github.com/nicoSWD
 *
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */

namespace nicoSWD\Rules\Compiler\Exception;

class MissingOperatorException extends \Exception
{
    protected $message = 'Missing operator';
}
