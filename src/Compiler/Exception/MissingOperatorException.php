<?php declare(strict_types=1);

/**
 * @license     http://opensource.org/licenses/mit-license.php MIT
 * @link        https://github.com/nicoSWD
 * @author      Nicolas Oelgart <nico@oelgart.com>
 */
namespace nicoSWD\Rule\Compiler\Exception;

final class MissingOperatorException extends \Exception
{
    /** @var string */
    protected $message = 'Missing operator';
}
